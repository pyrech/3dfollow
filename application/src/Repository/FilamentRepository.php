<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Filament;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Filament>
 */
class FilamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filament::class);
    }

    public function getQueryBuilderForOwner(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('f.id', 'DESC')
        ;
    }
}
