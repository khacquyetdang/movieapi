<?php

namespace App\Controller;

use App\Security\TokenStorage;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokensController extends AbstractController
{
    use ControllerTrait;

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

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder,
        TokenStorage $tokenStorage) {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /*
     * @Rest\View(statusCode=201)
     */
    public function postTokenAction(Request $request)
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
            'exp' => time() + 36000,
            'id' => $user->getId(),
        ]);

        $this->tokenStorage->storeToken($user->getEmail(), $token);
        return new JsonResponse(["token" => $token]);
    }
}
