<?php

namespace App\Controller;

use App\Entity\Filament;
use App\Entity\User;
use App\Form\FilamentType;
use App\Repository\FilamentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/filament", name="filament_")
 * @IsGranted("ROLE_PRINTER")
 */
class FilamentController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(FilamentRepository $filamentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('filament/index.html.twig', [
            'filaments' => $filamentRepository->findAllForOwner($user),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $filament = new Filament();
        $form = $this->createForm(FilamentType::class, $filament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filament->setOwner($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($filament);
            $entityManager->flush();

            return $this->redirectToRoute('filament_index');
        }

        return $this->render('filament/new.html.twig', [
            'filament' => $filament,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Filament $filament): Response
    {
        $this->assertOwner($filament);

        $form = $this->createForm(FilamentType::class, $filament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('filament_index');
        }

        return $this->render('filament/edit.html.twig', [
            'filament' => $filament,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Filament $filament): Response
    {
        $this->assertOwner($filament);

        if (count($filament->getPrintObjects()) > 0) {
            throw $this->createNotFoundException('Filament is not deletable');
        }

        if ($this->isCsrfTokenValid('filament-delete-' . $filament->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($filament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('filament_index');
    }

    private function assertOwner(Filament $filament): void
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $owner = $filament->getOwner();

        if (!$user || !$owner || $user->getId() !== $owner->getId()) {
            throw $this->createNotFoundException('Current user does not have access to this filament');
        }
    }
}
