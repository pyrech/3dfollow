<?php

namespace App\Controller;

use App\Entity\User;
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
     * @Route("/generate-join-token", name="generate_join_token", methods={"POST"})
     * @IsGranted("ROLE_PRINTER")
     */
    public function generateJoinToken(CsrfTokenManagerInterface $csrfTokenManager, TokenGeneratorInterface $tokenGenerator, Request $request): Response
    {
        $token = new CsrfToken('team_generate_join_token', $request->request->get('token'));

        if (!$csrfTokenManager->isTokenValid($token)) {
            $this->addFlash('danger', 'Invalid CSRF token');

            return $this->redirectToRoute('team_index');
        }
        /** @var User $user */
        $user = $this->getUser();

        $user->getTeamCreated()->setJoinToken($tokenGenerator->generateToken());

        $this->getDoctrine()->getManager()->flush();

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

            return $this->redirectToRoute('home_index');
        }

        $invitationManager->prepareToJoin($request, $team);

        return $this->render('team/join.html.twig', [
            'team' => $team,
        ]);
    }
}
