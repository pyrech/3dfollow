<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setSearchFields(['id', 'username', 'defaultLocale'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $username = TextField::new('username');
        $isPrinter = Field::new('isPrinter');
        $isAdmin = Field::new('isAdmin');
        $createdAt = DateTimeField::new('createdAt')->setDisabled(true);
        $teams = AssociationField::new('teams');
        $id = IntegerField::new('id', 'ID');
        $password = TextField::new('password');
        $lastChangelogSeenAt = DateTimeField::new('lastChangelogSeenAt');
        $defaultLocale = TextField::new('defaultLocale');
        $printRequests = AssociationField::new('printRequests');
        $filaments = AssociationField::new('filaments');
        $teamCreated = AssociationField::new('teamCreated');
        $printObjects = AssociationField::new('printObjects');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $isPrinter, $isAdmin, $createdAt, $teams];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $username, $isAdmin, $isPrinter, $createdAt, $lastChangelogSeenAt, $defaultLocale, $printRequests, $filaments, $teamCreated, $teams, $printObjects];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$username, $isPrinter, $isAdmin, $createdAt, $teams];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$username, $isPrinter, $isAdmin, $createdAt, $teams];
        }
    }
}
