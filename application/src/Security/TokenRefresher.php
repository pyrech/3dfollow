<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenRefresher
{
    public const PROVIDER_KEY = 'main';

    private AppLoginFormAuthenticator $authenticator;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        AppLoginFormAuthenticator $authenticator,
        TokenStorageInterface $tokenStorage
    ) {
        $this->authenticator = $authenticator;
        $this->tokenStorage = $tokenStorage;
    }

    public function refresh(User $user, Request $request): void
    {
        // create an authenticated token for the User
        $token = $this->authenticator->createAuthenticatedToken($user, self::PROVIDER_KEY);
        // authenticate this in the system
        $this->tokenStorage->setToken($token);
    }
}
