<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\ChangelogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/changelog', name: 'changelog_')]
class ChangelogController extends AbstractController
{
    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(ChangelogRepository $changelogRepository): Response
    {
        return $this->render('changelog/index.html.twig', [
            'changelogs' => $changelogRepository->findAllSorted(),
        ]);
    }

    #[Route(path: '/update-seen', name: 'update_seen', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateSeen(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setLastChangelogSeenAt(new \DateTime());

        $entityManager->flush();

        return new JsonResponse('ok');
    }
}
