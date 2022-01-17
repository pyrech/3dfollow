<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Filament;
use App\Entity\User;
use App\Form\FilamentType;
use App\Repository\FilamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/filament', name: 'filament_')]
#[IsGranted(data: 'ROLE_PRINTER')]
class FilamentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(FilamentRepository $filamentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('filament/index.html.twig', [
            'filaments' => $filamentRepository->findAllForOwner($user),
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $filament = new Filament();
        $form = $this->createForm(FilamentType::class, $filament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filament->setOwner($user);

            $this->entityManager->persist($filament);
            $this->entityManager->flush();

            return $this->redirectToRoute('filament_index');
        }

        return $this->render('filament/new.html.twig', [
            'filament' => $filament,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filament $filament): Response
    {
        $this->assertOwner($filament);

        $form = $this->createForm(FilamentType::class, $filament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('filament_index');
        }

        return $this->render('filament/edit.html.twig', [
            'filament' => $filament,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Filament $filament): Response
    {
        $this->assertOwner($filament);

        if (\count($filament->getPrintObjects()) > 0) {
            throw $this->createNotFoundException('Filament is not deletable');
        }

        if ($this->isCsrfTokenValid('filament-delete-' . $filament->getId(), (string) $request->request->get('_token'))) {
            $this->entityManager->remove($filament);
            $this->entityManager->flush();
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
