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
                'label' => 'Nom à donner au filament pour le retrouver facilement dans l\'application',
                'help' => 'Couleur, matière imitée, etc.',
            ])
            ->add('weight', null, [
                'label' => 'Poids de la bobine (en grammes)',
            ])
            ->add('weightUsed', null, [
                'label' => 'Quantité de filament déjà utilisé (en grammes)',
                'help' => 'Permet de corriger la consommation si toutes vos impressions n\'ont pas été enregistrées dans l\'application',
                'required' => false,
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
