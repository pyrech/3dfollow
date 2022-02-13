<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use App\Repository\PrintRequestRepository;
use App\Repository\TeamRepository;
use App\Team\InvitationManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route(path: '/team', name: 'team_')]
class TeamController extends AbstractController
{
    #[Route(path: '', name: 'index', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_PRINTER')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('team/index.html.twig', [
            'team' => $user->getTeamCreated(),
        ]);
    }

    #[Route(path: '/print-requests', name: 'print_requests', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_PRINTER')]
    public function printRequests(PrintRequestRepository $printRequestRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $team = $user->getTeamCreated();

        $printRequests = $team ? $printRequestRepository->findAllForTeam($team) : [];

        return $this->render('team/print_requests.html.twig', [
            'print_requests' => $printRequests,
        ]);
    }

    #[Route(path: '/generate-join-token', name: 'generate_join_token', methods: ['POST'])]
    #[IsGranted(data: 'ROLE_PRINTER')]
    public function generateJoinToken(
        EntityManagerInterface $entityManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        TokenGeneratorInterface $tokenGenerator,
        Request $request
    ): Response {
        $token = new CsrfToken('team_generate_join_token', (string) $request->request->get('token'));

        if (!$csrfTokenManager->isTokenValid($token)) {
            $this->addFlash('danger', 'common.csrf_token_error');

            return $this->redirectToRoute('team_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $team = $user->getTeamCreated();

        if (!$team) {
            $team = new Team();
            $user->setTeamCreated($team);
        }

        $team->setJoinToken($tokenGenerator->generateToken());

        $entityManager->flush();

        return $this->redirectToRoute('team_index');
    }

    #[Route(path: '/join/{token}', name: 'join')]
    public function join(
        TeamRepository $repository,
        InvitationManager $invitationManager,
        Request $request,
        string $token
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();

        $team = $repository->findOneByJoinToken($token);

        if (!$team) {
            throw $this->createNotFoundException('Unknown token');
        }

        if ($user) {
            $invitationManager->join($request, $user, $team);

            return $this->redirectToRoute('dashboard_index');
        }

        $invitationManager->prepareToJoin($request, $team);

        return $this->render('team/join.html.twig', [
            'team' => $team,
        ]);
    }
}
