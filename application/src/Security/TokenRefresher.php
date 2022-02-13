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
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class TokenRefresher
{
    final public const PROVIDER_KEY = 'main';

    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private AppLoginFormAuthenticator $authenticator,
    ) {
    }

    public function refresh(User $user, Request $request): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->authenticator,
            $request,
        );
    }
}
