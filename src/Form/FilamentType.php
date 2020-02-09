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
                'label' => 'Nom à présenter aux utilisateurs',
                'help' => 'Couleur, matière imitée, etc.'
            ])
            ->add('weight', null, [
                'label' => 'Poids de la bobine (en grammes)',
            ])
            ->add('price', null, [
                'label' => 'Prix de la bobine (en €)',
            ])
            ->add('density', null, [
                'label' => 'Densité (en g/cm³)',
            ])
            ->add('diameter', null, [
                'label' => 'Diamètre (en mm)',
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
