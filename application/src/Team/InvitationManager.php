<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Team;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\TeamRepository;
use App\Security\TokenRefresher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\Translation\TranslatorInterface;

class InvitationManager
{
    private const JOIN_SESSION_KEY = 'team_join';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
        private readonly TranslatorInterface $translator,
        private readonly TokenRefresher $tokenRefresher,
    ) {
    }

    public function join(Request $request, User $user, Team $team): void
    {
        $teamCreator = $team->getCreator();

        if ($team->getMembers()->contains($user)) {
            $this->getFlashBag($request)->add('warning', $this->translator->trans('team.join.flash.warning', [
                '%username%' => $teamCreator ? $teamCreator->getUsername() : '',
            ]));

            return;
        }

        $team->addMember($user);

        $this->entityManager->flush();

        $this->getFlashBag($request)->add('success', $this->translator->trans('team.join.flash.success', [
            '%username%' => $teamCreator ? $teamCreator->getUsername() : '',
        ]));

        // Roles of users may have changed if joining its first group, so let's refresh its token to avoid logout
        $this->tokenRefresher->refresh($user, $request);
    }

    public function prepareToJoin(Request $request, Team $team): void
    {
        $request->getSession()->set(self::JOIN_SESSION_KEY, $team->getId());
    }

    public function isInvitationInProgress(Request $request): bool
    {
        return $request->getSession()->has(self::JOIN_SESSION_KEY);
    }

    public function handleUser(Request $request, User $user): void
    {
        if (!$request->getSession()->has(self::JOIN_SESSION_KEY)) {
            return;
        }

        $team = $this->teamRepository->find($request->getSession()->get(self::JOIN_SESSION_KEY));
        $request->getSession()->remove(self::JOIN_SESSION_KEY);

        if (!$team) {
            return;
        }

        $this->join($request, $user, $team);
    }

    private function getFlashBag(Request $request): FlashBagInterface
    {
        $session = $request->getSession();

        if (!$session instanceof Session) {
            throw new \RuntimeException('Wrong session instance');
        }

        return $session->getFlashBag();
    }
}
