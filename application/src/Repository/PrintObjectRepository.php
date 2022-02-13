<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\PrintObject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrintObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintObject[]    findAll()
 * @method PrintObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintObject::class);
    }

    public function getQueryBuilderForUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.printedAt', 'DESC')
        ;
    }
}
