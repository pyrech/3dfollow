<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\PrintRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class PrintRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PrintRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Print request')
            ->setEntityLabelInPlural('Print requests')
            ->setSearchFields(['id', 'name', 'link', 'comment', 'quantity'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $team = AssociationField::new('team');
        $user = AssociationField::new('user');
        $name = TextField::new('name');
        $link = UrlField::new('link');
        $comment = TextareaField::new('comment');
        $quantity = IntegerField::new('quantity');
        $printObjects = AssociationField::new('printObjects');
        $createdAt = DateTimeField::new('createdAt');
        $id = IntegerField::new('id', 'ID');
        $isPrinted = Field::new('isPrinted');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$team, $user, $name, $link, $quantity, $isPrinted];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $link, $comment, $isPrinted, $quantity, $createdAt, $user, $team, $printObjects];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$team, $user, $name, $link, $comment, $quantity, $printObjects, $createdAt];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$team, $user, $name, $link, $comment, $quantity, $printObjects, $createdAt];
        }
    }
}
