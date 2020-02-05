<?php

namespace App\Form;

use App\Entity\Filament;
use App\Entity\PrintItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'objet',
            ])
            ->add('link', null, [
                'label' => 'Lien du modèle (sur www.thingiverse.com)',
                'attr' => [
                    'placeholder' => 'https://www.thingiverse.com/thing:XXXXXXX',
                ],
            ])
            ->add('filament', EntityType::class, [
                'label' => 'Filament',
                'class' => Filament::class,
                'required' => false,
            ])
            ->add('comment', null, [
                'label' => 'Commentaire (optionnel)',
                'help' => 'Autre filament, dimensions à changer, etc.',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrintItem::class,
        ]);
    }
}
