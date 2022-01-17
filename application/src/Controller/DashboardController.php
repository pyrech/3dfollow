<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\PrintRequestRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard", name="dashboard_")
 * @IsGranted("ROLE_USER")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PrintRequestRepository $printRequestRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $memberPrintRequests = [];
        $teamPrintRequests = [];

        if ($this->isGranted('ROLE_PRINTER')) {
            $team = $user->getTeamCreated();

            if ($team) {
                $teamPrintRequests = $printRequestRepository->findLatestPendingForTeam($team);
            }
        } elseif ($this->isGranted('ROLE_TEAM_MEMBER')) {
            $memberPrintRequests = $printRequestRepository->findLatestPendingForUser($user);
        }

        return $this->render('dashboard/index.html.twig', [
            'member_print_requests' => $memberPrintRequests,
            'team_print_requests' => $teamPrintRequests,
        ]);
    }
}
