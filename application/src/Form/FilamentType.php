<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Filament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'filament.form.name.label',
                'help' => 'filament.form.name.help',
            ])
            ->add('weight', null, [
                'label' => 'filament.form.weight.label',
            ])
            ->add('weightUsed', null, [
                'label' => 'filament.form.weightUsed.label',
                'help' => 'filament.form.weightUsed.help',
                'required' => false,
            ])
            ->add('price', null, [
                'label' => 'filament.form.price.label',
            ])
            ->add('density', null, [
                'label' => 'filament.form.density.label',
            ])
            ->add('diameter', null, [
                'label' => 'filament.form.diameter.label',
            ])
            ->add('comment', null, [
                'label' => 'filament.form.comment.label',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Filament::class,
        ]);
    }
}
