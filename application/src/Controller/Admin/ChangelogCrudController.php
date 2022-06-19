<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Changelog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ChangelogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Changelog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Changelog')
            ->setEntityLabelInPlural('Changelogs')
            ->setSearchFields(['id', 'items'])
            ->showEntityActionsInlined()
            ->setDefaultSort(['date' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $date = DateTimeField::new('date');
        $items = ArrayField::new('items');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $date, $items];
        }

        if (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [$date, $items];
        }

        return [$date];
    }
}
