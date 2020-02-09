<?php

namespace App\Controller;

use App\Entity\PrintItem;
use App\Form\PrintItemType;
use App\Repository\PrintItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintItemController extends AbstractController
{
    /**
     * @Route("/", name="print_item_index", methods={"GET"})
     */
    public function index(PrintItemRepository $printItemRepository): Response
    {
        return $this->render('print_item/index.html.twig', [
            'print_items' => $printItemRepository->findAllForUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/print-item/new", name="print_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $printItem = new PrintItem();
        $form = $this->createForm(PrintItemType::class, $printItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $printItem->setUser($this->getUser());

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

        $form = $this->createForm(PrintItemType::class, $printItem);
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
            throw $this->createNotFoundException();
        }
    }
}
