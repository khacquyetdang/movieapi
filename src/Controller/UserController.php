<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class UserController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
    }
    /**
     * @Route("/user/token", name="user")
     * @Method("POST")
     */
    public function tokenAction(Request $request)
    {

        $user = $this->getDoctrine()->getRepository("App:User")
            ->findOneBy(["email" => $request->getUser()]);
        if (!$user) {
            throw new BadCredentialsException();
        }

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword(), $user->getSalt());

        if (!$isPasswordValid) {
            throw new BadCredentialsException("Password is not correct");
        }

        $token = $this->jwtEncoder->encode([
            'email' => $user->getEmail(),
            'exp' => time() + 3600,
            'id' => $user->getId(),
        ]);

        return new JsonResponse(["token" => $token]);
    }
}
