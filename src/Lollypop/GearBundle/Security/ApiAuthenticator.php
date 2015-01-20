<?php

namespace Lollypop\GearBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ApiAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {

    private $userProvider;

    public function __construct(UserProviderInterface $userProvider) {
        $this->userProvider = $userProvider;
    }

    public function createToken(Request $request, $providerKey) {
        $apiKey = null;

        if ($request->headers->has('x-auth')) {
            $apiKey = $request->headers->get('x-auth');
        } else if ($request->headers->has('authorization')) {
            $apiKey = $request->headers->get('authorization');
        } else if ($request->query->has('x-auth')) {
            $apiKey = $request->query->get('x-auth');
        }

        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
                'anon.', $apiKey, $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        $apiKey = $token->getCredentials();
        $username = $this->userProvider->getUsernameForApiKey($apiKey);

        if (!$username) {
            throw new AuthenticationException(
            sprintf('API Key "%s" does not exist.', $apiKey)
            );
        }

        $user = $this->userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
                $user, $apiKey, $providerKey, $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {

        $apiRoutePrefix = 'lollypop_gear_api_';
        $route = $request->attributes->get('_route');
        if (strpos($route, $apiRoutePrefix) === 0) {
            if ($request->headers->has('X-Auth')) {
                $GCMRegID = $request->headers->get('X-Auth');
            }
            if ($request->headers->has('Authorization')) {
                $GCMRegID = $request->headers->get('Authorization');
            }
            if ($request->query->has('X-Auth')) {
                $GCMRegID = $request->query->get('X-Auth');
            }
            if (!empty($GCMRegID)) {

                return new JsonResponse(array('error' => true, 'message' => 'Header has invalid data'));
            }

            return new JsonResponse(array('error' => true, 'message' => 'Required header is missing in request'));
        }

        return new Response("Authentication Failed. " . $exception->getMessage(), 403);
    }

}
