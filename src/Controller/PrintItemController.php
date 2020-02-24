<?php

namespace App\Controller;

use App\Entity\PrintItem;
use App\Entity\Team;
use App\Entity\User;
use App\Form\PrintItemType;
use App\Repository\PrintItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintItemController extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     * @Route("/", name="print_item_index", methods={"GET"})
     */
    public function index(PrintItemRepository $printItemRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $teamsById = [];

        foreach ($user->getTeams() as $team) {
            $teamsById[$team->getId()] = $team;
        }

        $printItemsByTeam = [];
        $printItems = $printItemRepository->findAllForUser($user);

        foreach ($printItems as $printItem) {
            $printItemsByTeam[$printItem->getTeam()->getId()][] = $printItem;
        }

        return $this->render('print_item/index.html.twig', [
            'teams' => $teamsById,
            'print_items_by_team' => $printItemsByTeam,
        ]);
    }

    /**
     * @Route("/print-item/new/{id}", name="print_item_new", methods={"GET","POST"})
     */
    public function new(Request $request, Team $team): Response
    {
        $printItem = new PrintItem();
        $form = $this->createForm(PrintItemType::class, $printItem, [
            'is_printed' => false,
            'team' => $team,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $printItem->setUser($this->getUser());
            $printItem->setTeam($team);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($printItem);
            $entityManager->flush();

            return $this->redirectToRoute('print_item_index');
        }

        return $this->render('print_item/new.html.twig', [
            'print_item' => $printItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/print-item/{id}/edit", name="print_item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PrintItem $printItem): Response
    {
        $this->assertUser($printItem);

        $form = $this->createForm(PrintItemType::class, $printItem, [
            'is_printed' => $printItem->getIsPrinted(),
            'team' => $printItem->getTeam(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('print_item_index');
        }

        return $this->render('print_item/edit.html.twig', [
            'print_item' => $printItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/print-item/{id}", name="print_item_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PrintItem $printItem): Response
    {
        $this->assertUser($printItem);

        if ($printItem->getIsPrinted()) {
            throw $this->createNotFoundException('Item is not deletable');
        }

        if ($this->isCsrfTokenValid('delete'.$printItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($printItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('print_item_index');
    }

    private function assertUser(PrintItem $printItem): void
    {
        $user = $this->getUser();

        if (!$user || $user->getId() !== $printItem->getUser()->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this item');
        }
    }
}
