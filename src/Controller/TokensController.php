<?php

namespace App\Controller;

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

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $jwtEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtEncoder = $jwtEncoder;
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
            'exp' => time() + 3600,
            'id' => $user->getId(),
        ]);

        return new JsonResponse(["token" => $token]);
    }
}
