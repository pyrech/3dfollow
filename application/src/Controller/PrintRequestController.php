<?php

namespace App\Controller;

use App\Entity\PrintRequest;
use App\Entity\Team;
use App\Entity\User;
use App\Form\PrintRequestType;
use App\Repository\PrintRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintRequestController extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     * @Route("/", name="print_request_index", methods={"GET"})
     */
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
            $printRequestsByTeam[$printRequest->getTeam()->getId()][] = $printRequest;
        }

        return $this->render('print_request/index.html.twig', [
            'teams' => $teamsById,
            'print_requests_by_team' => $printRequestsByTeam,
        ]);
    }

    /**
     * @Route("/print-request/new/{id}", name="print_request_new", methods={"GET","POST"})
     */
    public function new(Request $request, Team $team): Response
    {
        $printRequest = new PrintRequest();
        $form = $this->createForm(PrintRequestType::class, $printRequest, [
            'is_printed' => false,
            'team' => $team,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $printRequest->setUser($this->getUser());
            $printRequest->setTeam($team);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($printRequest);
            $entityManager->flush();

            return $this->redirectToRoute('print_request_index');
        }

        return $this->render('print_request/new.html.twig', [
            'print_request' => $printRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/print-request/{id}/edit", name="print_request_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PrintRequest $printRequest): Response
    {
        $this->assertUser($printRequest);

        $form = $this->createForm(PrintRequestType::class, $printRequest, [
            'is_printed' => $printRequest->getIsPrinted(),
            'team' => $printRequest->getTeam(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('print_request_index');
        }

        return $this->render('print_request/edit.html.twig', [
            'print_request' => $printRequest,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/print-request/{id}", name="print_request_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PrintRequest $printRequest): Response
    {
        $this->assertUser($printRequest);

        if ($printRequest->getIsPrinted()) {
            throw $this->createNotFoundException('Print request is not deletable');
        }

        if ($this->isCsrfTokenValid('delete'.$printRequest->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($printRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('print_request_index');
    }

    private function assertUser(PrintRequest $printRequest): void
    {
        $user = $this->getUser();

        if (!$user || $user->getId() !== $printRequest->getUser()->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this request');
        }
    }
}
