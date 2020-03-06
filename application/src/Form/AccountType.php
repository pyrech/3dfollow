<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'account.index.form.username.label',
            ])
            ->add('isPrinter', ChoiceType::class, [
                'label' => 'account.index.form.isPrinter.label',
                'required' => true,
                'choices' => [
                    'common.yes' => true,
                    'common.no' => false,
                ],
                'expanded' => true,
            ])
            ->add('oldPassword', PasswordType::class, [
                'label' => 'account.index.form.oldPassword.label',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.old_password_required',
                        'groups' => ['password_change'],
                    ]),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'account.index.form.newPassword.label',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'groups' => ['password_change'],
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'validation.password_length',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'groups' => ['password_change'],
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);

        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $groups = ['Default'];

            if ($form->get('oldPassword')->getData() || $form->get('newPassword')->getData()) {
                $groups[] = 'password_change';
            }

            return $groups;
        });
    }
}
