<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    
    public function supports(Request $request)
    {
        return true;
    }

    public function getCredentials(Request $request)
    {
       if ($request->headers->get('X-AUTH-TOKEN')==null || empty($request->headers->get('X-AUTH-TOKEN')))
       {
           return false;
       } else {
           return [
               'apiToken' => $request->headers->get('X-AUTH-TOKEN'),
           ];
       }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $get_token = $this->userRepository->findOneBy([
            'token' => $credentials['apiToken'],
        ]);
        return $get_token;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Pas les droits !", 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
