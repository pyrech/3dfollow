<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenRefresher
{
    final public const PROVIDER_KEY = 'main';

    private readonly AppLoginFormAuthenticator $authenticator;
    private readonly TokenStorageInterface $tokenStorage;

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
