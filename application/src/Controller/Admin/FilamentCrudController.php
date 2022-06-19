<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\Filament;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FilamentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Filament::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Filament')
            ->setEntityLabelInPlural('Filaments')
            ->setSearchFields(['id', 'name', 'weight', 'weightUsed', 'price', 'density', 'diameter', 'comment'])
            ->showEntityActionsInlined()
            ->setDefaultSort(['createdAt' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $owner = AssociationField::new('owner');
        $name = TextField::new('name');
        $weight = NumberField::new('weight');
        $price = NumberField::new('price');
        $density = NumberField::new('density');
        $diameter = NumberField::new('diameter');
        $comment = TextareaField::new('comment');
        $id = IntegerField::new('id', 'ID');
        $weightUsed = NumberField::new('weightUsed');
        $printObjects = AssociationField::new('printObjects');
        $createdAt = DateTimeField::new('createdAt');

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $weight, $weightUsed, $price, $density, $diameter, $comment, $owner, $printObjects, $createdAt];
        }

        if (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [$owner, $name, $weight, $price, $density, $diameter, $comment, $createdAt];
        }

        return [$owner, $name, $weight, $price, $density, $diameter, $createdAt];
    }
}
