<?php

namespace App\Controller;

use App\Entity\EntityMerger;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Security\TokenStorage;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class UsersController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var EntityMerger
     */
    private $entityMerger;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
        JWTEncoderInterface $jwtEncoder,
        EntityMerger $entityMerger,
        TokenStorage $tokenStorage) {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->entityMerger = $entityMerger;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Rest\View
     * @Security("is_granted('show', theUser)", message="Access denied")
     */
    public function getUserAction(?User $theUser)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }
        return $theUser;
    }

    /**
     * @param User $user
     * @Rest\View(statusCode=201)
     * @Rest\NoRoute
     * @ParamConverter("user", converter="fos_rest.request_body",
     * options={"deserializationContext"={"groups"={"Deserialize"}}})
     */
    public function postUserAction(User $user, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $this->encodePassword($user);
        $user->setRoles([User::ROLE_USER]);
        $this->persistUser($user);
        return $user;
    }

    private function persistUser($user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist(($user));
        $em->flush();

    }
    private function encodePassword($user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }
    /**
     * @Rest\NoRoute()
     * @ParamConverter("modifiedUser", converter="fos_rest.request_body",
     *      options={
     *           "validator"={"groups"={"Patch"}},
     *           "deserializationContext"={"groups"={"Deserialize"}}
     *      }
     * )
     * @Security("is_granted('edit', theUser)", message="Access denied")
     */
    public function patchUserAction(?User $theUser,
        User $modifiedUser,
        ConstraintViolationListInterface $validationErrors) {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        if (empty($modifiedUser->getPassword())) {
            $modifiedUser->setPassword(null);
        }

        $this->entityMerger->merge($theUser, $modifiedUser);
        $this->encodePassword($theUser);
        $this->persistUser($theUser);
        if ($modifiedUser->getPassword()) {
            $this->tokenStorage->invalidateToken($theUser->getUsername());
        }

        return $theUser;
    }
}
