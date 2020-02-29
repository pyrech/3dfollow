<?php

namespace App\Controller;

use App\Entity\PrintObject;
use App\Entity\User;
use App\Form\PrintObjectType;
use App\Repository\PrintObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/print-object", name="print_object_")
 * @IsGranted("ROLE_PRINTER")
 */
class PrintObjectController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PrintObjectRepository $printObjectRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $printObjects = $printObjectRepository->findAllForUser($user);

        return $this->render('print_object/index.html.twig', [
            'print_objects' => $printObjects,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        $printObject = new PrintObject();
        $form = $this->createForm(PrintObjectType::class, $printObject, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $printObject->setUser($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($printObject);
            $entityManager->flush();

            return $this->redirectToRoute('print_object_index');
        }

        return $this->render('print_object/new.html.twig', [
            'print_object' => $printObject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PrintObject $printObject): Response
    {
        $this->assertUser($printObject);

        $user = $this->getUser();

        $form = $this->createForm(PrintObjectType::class, $printObject, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('print_object_index');
        }

        return $this->render('print_object/edit.html.twig', [
            'print_object' => $printObject,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, PrintObject $printObject): Response
    {
        $this->assertUser($printObject);

        if ($this->isCsrfTokenValid('delete-print-object-'.$printObject->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($printObject);
            $entityManager->flush();
        }

        return $this->redirectToRoute('print_object_index');
    }

    private function assertUser(PrintObject $printObject): void
    {
        $user = $this->getUser();

        if (!$user || $user->getId() !== $printObject->getUser()->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this object');
        }
    }
}
