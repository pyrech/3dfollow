<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/terms", name="home_terms", methods={"GET"})
     */
    public function terms(): Response
    {
        return $this->render('home/terms.html.twig');
    }

    /**
     * @Route("/privacy", name="home_privacy", methods={"GET"})
     */
    public function privacy(): Response
    {
        return $this->render('home/privacy.html.twig');
    }
}
