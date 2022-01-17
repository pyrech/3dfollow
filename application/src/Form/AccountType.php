<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountType extends AbstractType
{
    /** @var array<string, string> */
    private array $localeLabels;

    /**
     * @param array<string, string> $localeLabels
     */
    public function __construct(array $localeLabels)
    {
        $this->localeLabels = $localeLabels;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('defaultLocale', ChoiceType::class, [
                'label' => 'account.index.form.defaultLocale.label',
                'required' => false,
                'choices' => array_flip($this->localeLabels),
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

    public function configureOptions(OptionsResolver $resolver): void
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
