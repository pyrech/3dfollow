<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PrintRequestRepository;
use App\Repository\TeamRepository;
use App\Team\InvitationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @Route("/team", name="team_")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @IsGranted("ROLE_PRINTER")
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('team/index.html.twig', [
            'team' => $user->getTeamCreated(),
        ]);
    }

    /**
     * @Route("/print-requests", name="print_requests", methods={"GET"})
     * @IsGranted("ROLE_PRINTER")
     */
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

    /**
     * @Route("/generate-join-token", name="generate_join_token", methods={"POST"})
     * @IsGranted("ROLE_PRINTER")
     */
    public function generateJoinToken(CsrfTokenManagerInterface $csrfTokenManager, TokenGeneratorInterface $tokenGenerator, Request $request): Response
    {
        $token = new CsrfToken('team_generate_join_token', $request->request->get('token'));

        if (!$csrfTokenManager->isTokenValid($token)) {
            $this->addFlash('danger', 'common.csrf_token_error');

            return $this->redirectToRoute('team_index');
        }

        /** @var User $user */
        $user = $this->getUser();
        $team = $user->getTeamCreated();

        if ($team) {
            $team->setJoinToken($tokenGenerator->generateToken());

            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('team_index');
    }

    /**
     * @Route("/join/{token}", name="join")
     */
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
