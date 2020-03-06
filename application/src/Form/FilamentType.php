<?php

namespace App\Form;

use App\Entity\Filament;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filament::class,
        ]);
    }
}
