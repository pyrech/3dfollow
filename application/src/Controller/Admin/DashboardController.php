<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
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
        yield MenuItem::linkTo(PrintObjectCrudController::class, 'Print object', 'far fa-file-code');
        yield MenuItem::linkTo(PrintRequestCrudController::class, 'Print request', 'fas fa-list');
        yield MenuItem::linkTo(FilamentCrudController::class, 'Filament', 'fas fa-circle-notch');
        yield MenuItem::linkTo(UserCrudController::class, 'User', 'fas fa-user');
        yield MenuItem::linkTo(TeamCrudController::class, 'Team', 'fas fa-users');
        yield MenuItem::linkTo(ChangelogCrudController::class, 'Changelog', 'fas fa-book');
    }

    #[Route(path: '/admin')]
    public function index(): Response
    {
        return $this->redirect(
            $this->generateUrl('admin_print_object_index')
        );
    }
}
