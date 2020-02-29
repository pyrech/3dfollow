<?php

namespace App\Form;

use App\Entity\PrintRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isPrinted = $options['is_printed'];

        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'objet',
                'disabled' => $isPrinted,
            ])
            ->add('link', null, [
                'label' => 'Lien du modèle (sur www.thingiverse.com)',
                'attr' => [
                    'placeholder' => 'https://www.thingiverse.com/thing:XXXXXXX',
                ],
                'disabled' => $isPrinted,
            ])
            ->add('quantity', null, [
                'label' => 'Quantité',
                'disabled' => $isPrinted,
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
            'data_class' => PrintRequest::class,
        ]);

        $resolver->setRequired('is_printed');
        $resolver->setAllowedTypes('is_printed', 'bool');
    }
}
