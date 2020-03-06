<?php

namespace App\Form;

use App\Entity\PrintRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isPrinted = $options['is_printed'];

        $builder
            ->add('name', null, [
                'label' => 'print_request.form.name.label',
                'disabled' => $isPrinted,
            ])
            ->add('link', null, [
                'label' => 'print_request.form.link.label',
                'attr' => [
                    'placeholder' => 'print_request.form.link.placeholder',
                ],
                'disabled' => $isPrinted,
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'print_request.form.quantity.label',
                'disabled' => $isPrinted,
            ])
            ->add('comment', null, [
                'label' => 'print_request.form.comment.label',
                'help' => 'print_request.form.comment.help',
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
