<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Team')
            ->setEntityLabelInPlural('Teams')
            ->setSearchFields(['id', 'joinToken'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $creator = AssociationField::new('creator');
        $members = AssociationField::new('members');
        $id = IntegerField::new('id', 'ID');
        $joinToken = TextField::new('joinToken');
        $printRequests = AssociationField::new('printRequests');

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $joinToken, $creator, $members, $printRequests];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$creator, $members];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$creator, $members];
        }

        // Index page
        return [$creator, $members, $joinToken];
    }
}
