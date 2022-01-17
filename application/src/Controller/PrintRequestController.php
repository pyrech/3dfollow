<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\PrintRequest;
use App\Entity\Team;
use App\Entity\User;
use App\Form\PrintRequestType;
use App\Repository\PrintRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/print-request', name: 'print_request_')]
class PrintRequestController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_TEAM_MEMBER')]
    public function index(PrintRequestRepository $printRequestRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $teamsById = [];

        foreach ($user->getTeams() as $team) {
            $teamsById[$team->getId()] = $team;
        }

        $printRequestsByTeam = [];
        $printRequests = $printRequestRepository->findAllForUser($user);

        foreach ($printRequests as $printRequest) {
            $printRequestTeam = $printRequest->getTeam();

            if (!$printRequestTeam) {
                continue;
            }

            $printRequestsByTeam[$printRequestTeam->getId()][] = $printRequest;
        }

        return $this->render('print_request/index.html.twig', [
            'teams' => $teamsById,
            'print_requests_by_team' => $printRequestsByTeam,
        ]);
    }

    #[Route(path: '/new/{id}', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_TEAM_MEMBER')]
    public function new(Request $request, Team $team): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $printRequest = new PrintRequest();
        $form = $this->createForm(PrintRequestType::class, $printRequest, [
            'is_printed' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $printRequest->setUser($user);
            $printRequest->setTeam($team);

            $this->entityManager->persist($printRequest);
            $this->entityManager->flush();

            return $this->redirectToRoute('print_request_index');
        }

        return $this->render('print_request/new.html.twig', [
            'print_request' => $printRequest,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted(data: 'ROLE_TEAM_MEMBER')]
    public function edit(Request $request, PrintRequest $printRequest): Response
    {
        $this->assertUser($printRequest);

        $form = $this->createForm(PrintRequestType::class, $printRequest, [
            'is_printed' => $printRequest->getIsPrinted(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('print_request_index');
        }

        return $this->render('print_request/edit.html.twig', [
            'print_request' => $printRequest,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted(data: 'ROLE_PRINTER')]
    public function show(Request $request, PrintRequest $printRequest): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $teamCreated = $user->getTeamCreated();
        $printRequestTeam = $printRequest->getTeam();

        if (!$teamCreated || !$printRequestTeam || $teamCreated->getId() !== $printRequestTeam->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this request');
        }

        return $this->render('print_request/show.html.twig', [
            'print_request' => $printRequest,
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    #[IsGranted(data: 'ROLE_TEAM_MEMBER')]
    public function delete(Request $request, PrintRequest $printRequest): Response
    {
        $this->assertUser($printRequest);

        if ($printRequest->getIsPrinted()) {
            throw $this->createNotFoundException('Print request is not deletable');
        }

        if ($this->isCsrfTokenValid('delete-print-request-' . $printRequest->getId(), (string) $request->request->get('_token'))) {
            $this->entityManager->remove($printRequest);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('print_request_index');
    }

    private function assertUser(PrintRequest $printRequest): void
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $printRequestUser = $printRequest->getUser();

        if (!$user || !$printRequestUser || $user->getId() !== $printRequestUser->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this request');
        }
    }
}
