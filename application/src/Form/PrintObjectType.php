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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrintObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        $builder
            ->add('name', null, [
                'label' => 'Nom de l\'objet',
            ])
            ->add('filament', EntityType::class, [
                'label' => 'Filament',
                'class' => Filament::class,
                'required' => false,
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
            ->add('quantity', null, [
                'label' => 'Quantité',
            ])
            ->add('length', null, [
                'label' => 'Longueur de filament utilisé (en mm)',
            ])
            ->add('cost', null, [
                'label' => 'Coût de filament utilisé (en €)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrintObject::class,
        ]);

        $resolver->setRequired('user');
        $resolver->setAllowedTypes('user', User::class);
    }
}
