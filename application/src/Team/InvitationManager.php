<?php

namespace App\Team;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class InvitationManager
{
    private const JOIN_SESSION_KEY = 'team_join';

    private $entityManager;
    private $teamRepository;

    public function __construct(EntityManagerInterface $entityManager, TeamRepository $teamRepository)
    {
        $this->entityManager = $entityManager;
        $this->teamRepository = $teamRepository;
    }

    public function join(Request $request, User $user, Team $team): void
    {
        if ($team->getMembers()->contains($user)) {
            $request->getSession()->getFlashBag()->add('warning', sprintf('Vous êtes déjà membre du groupe de %s.', $team->getCreator()->getUsername()));
            return;
        }

        $team->addMember($user);

        $this->entityManager->flush();

        $request->getSession()->getFlashBag()->add('success', sprintf('Vous êtes désormais membre du groupe de %s.', $team->getCreator()->getUsername()));
    }

    public function prepareToJoin(Request $request, Team $team): void
    {
        $request->getSession()->set(self::JOIN_SESSION_KEY, $team->getId());
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
}
