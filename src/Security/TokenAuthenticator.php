<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{

    /**
     * @var JWTEncoderInterface
     */
    private $jwtEncoder;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(JWTEncoderInterface $jwtEncoder,
        TokenStorage $tokenStorage) {
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if (!$token) {
            return false;
        }
        return true;

    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');
        $token = $extractor->extract($request);
        if (!$token) {
            return null;
        }

        return $token;

        /*
    return array(
    'token' => $request->headers->get('X-AUTH-TOKEN'),
    );
     */
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
            if ($data === false) {
                return null;
            }
            if (!$this->tokenStorage->isTokenValid($data['email'], $credentials)) {
                return null;
            }
            return $userProvider->loadUserByUsername($data['email']);
        } catch (JWTDecodeFailureException $exception) {
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case
        //return 'abcde' === $credentials;
        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $message = $authException->getMessage() ? $authException->getMessage() : 'Authentication Required';
        $data = array(
            // you might translate this message
            'message' => $message,
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
