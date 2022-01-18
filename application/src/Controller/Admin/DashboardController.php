<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Changelog;
use App\Entity\Filament;
use App\Entity\PrintObject;
use App\Entity\PrintRequest;
use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('3D Follow')
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Print object', 'far fa-file-code', PrintObject::class);
        yield MenuItem::linkToCrud('Print request', 'fas fa-list', PrintRequest::class);
        yield MenuItem::linkToCrud('Filament', 'fas fa-circle-notch', Filament::class);
        yield MenuItem::linkToCrud('User', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Team', 'fas fa-users', Team::class);
        yield MenuItem::linkToCrud('Changelog', 'fas fa-book', Changelog::class);
    }

    #[Route(path: '/admin')]
    public function index(): Response
    {
        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setController(PrintObjectCrudController::class)
            ->generateUrl()
        );
    }
}
