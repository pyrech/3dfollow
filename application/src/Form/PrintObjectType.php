<?php

namespace App\Form;

use App\Entity\Filament;
use App\Entity\PrintObject;
use App\Entity\PrintRequest;
use App\Entity\User;
use App\Repository\FilamentRepository;
use App\Repository\PrintRequestRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PrintObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'objet',
                'required' => true,
            ])
            ->add('filament', EntityType::class, [
                'label' => 'Filament',
                'class' => Filament::class,
                'query_builder' => function(FilamentRepository $filamentRepository) use ($user) {
                    return $filamentRepository->createQueryBuilder('f')
                        ->andWhere('f.owner = :user')
                        ->setParameter('user', $user)
                    ;
                },
            ])
            ->add('printRequest', EntityType::class, [
                'label' => 'Impression demandée',
                'class' => PrintRequest::class,
                'required' => false,
                'query_builder' => function(PrintRequestRepository $printRequestRepository) use ($user) {
                    return $printRequestRepository->createQueryBuilder('p')
                        ->andWhere('p.team = :team')
                        ->setParameter('team', $user->getTeamCreated())
                    ;
                },
                'group_by' => 'user',
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
            ])
            ->add('gCodeFile', VichFileType::class, [
                'label' => 'Select .gcode file',
                'required' => false,
                'allow_delete' => true,
                'download_link' => false,
                'attr' => [
                    'accept' => '.gcode',
                ]
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Masse de filament utilisé (en g)',
                'required' => false,
            ])
            ->add('length', NumberType::class, [
                'label' => 'Longueur de filament utilisé (en mm)',
                'required' => false,
            ])
            ->add('cost', NumberType::class, [
                'label' => 'Coût de filament utilisé (en €)',
                'required' => false,
            ])
        ;

        if (count($user->getTeamCreated()->getPrintRequests()) < 1) {
            $builder->remove('printRequest');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrintObject::class,
        ]);

        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);

        $resolver->setDefault('validation_groups', function (FormInterface $form) {
            $groups = ['Default'];

            /** @var PrintObject $data */
            $data = $form->getData();
            if (empty($data->getGCodeFile()) && empty($data->getGCode()->getName())) {
                $groups[] = 'no_gcode_uploaded';
            }

            return $groups;
        });
    }
}
