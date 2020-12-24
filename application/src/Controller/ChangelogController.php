<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ChangelogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/changelog", name="changelog_")
 */
class ChangelogController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(ChangelogRepository $changelogRepository): Response
    {
        return $this->render('changelog/index.html.twig', [
            'changelogs' => $changelogRepository->findAllSorted(),
        ]);
    }

    /**
     * @Route("/update-seen", name="update_seen", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function updateSeen(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setLastChangelogSeenAt(new \DateTime());

        $entityManager->flush();

        return new JsonResponse('ok');
    }
}
