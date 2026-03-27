<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Changelog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Changelog>
 */
class ChangelogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Changelog::class);
    }

    /**
     * @return Changelog[]
     */
    public function findAllSorted(): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->orderBy('c.date', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Changelog[]
     */
    public function getNewChangelogs(\DateTimeInterface $previous): array
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->orderBy('c.date', 'DESC')
            ->andWhere('c.date > :previous')
            ->setParameter('previous', $previous)
        ;

        return $qb->getQuery()->getResult();
    }
}
