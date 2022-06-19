<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Admin;

use App\Entity\PrintObject;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PrintObjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PrintObject::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Print object')
            ->setEntityLabelInPlural('Print objects')
            ->setSearchFields(['id', 'uuid', 'name', 'quantity', 'weight', 'length', 'cost', 'gCode.name', 'gCode.originalName', 'gCode.mimeType', 'gCode.size', 'gCode.dimensions'])
            ->showEntityActionsInlined()
            ->setDefaultSort(['printedAt' => 'DESC'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $user = AssociationField::new('user');
        $name = TextField::new('name');
        $filament = AssociationField::new('filament');
        $gCodeFile = Field::new('gCodeFile')->setFormType(VichFileType::class);
        $quantity = IntegerField::new('quantity');
        $weight = NumberField::new('weight');
        $length = NumberField::new('length');
        $cost = NumberField::new('cost');
        $printRequest = AssociationField::new('printRequest');
        $printedAt = DateTimeField::new('printedAt');
        $id = IntegerField::new('id', 'ID');
        $uuid = TextField::new('uuid');
        $updatedAt = DateTimeField::new('updatedAt');
        $gCodeName = TextField::new('gCode.name');
        $gCodeOriginalName = TextField::new('gCode.originalName');
        $gCodeMimeType = TextField::new('gCode.mimeType');
        $gCodeSize = IntegerField::new('gCode.size');
        $gCodeDimensions = ArrayField::new('gCode.dimensions');

        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $uuid, $name, $quantity, $weight, $length, $cost, $printedAt, $updatedAt, $gCodeName, $gCodeOriginalName, $gCodeMimeType, $gCodeSize, $gCodeDimensions, $filament, $user, $printRequest];
        }

        if (Crud::PAGE_NEW === $pageName || Crud::PAGE_EDIT === $pageName) {
            return [$user, $name, $filament, $gCodeFile, $quantity, $weight, $length, $cost, $printRequest, $printedAt];
        }

        return [$user, $name, $filament, $quantity, $cost, $printedAt];
    }
}
