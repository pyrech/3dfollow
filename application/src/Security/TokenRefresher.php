<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenRefresher
{
    const PROVIDER_KEY = 'main';

    private $authenticator;
    private $tokenStorage;

    public function __construct(
        AppLoginFormAuthenticator $authenticator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->authenticator = $authenticator;
        $this->tokenStorage = $tokenStorage;
    }

    public function refresh(User $user, Request $request)
    {
        // create an authenticated token for the User
        $token = $this->authenticator->createAuthenticatedToken($user, self::PROVIDER_KEY);
        // authenticate this in the system
        $this->tokenStorage->setToken($token);
    }
}
