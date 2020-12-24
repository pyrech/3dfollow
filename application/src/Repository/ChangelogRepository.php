<?php

namespace App\Repository;

use App\Entity\Changelog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Changelog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Changelog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Changelog[]    findAll()
 * @method Changelog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
