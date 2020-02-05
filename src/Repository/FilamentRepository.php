<?php

namespace App\Repository;

use App\Entity\Filament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Filament|null find($id, $lockMode = null, $lockVersion = null)
 * @method Filament|null findOneBy(array $criteria, array $orderBy = null)
 * @method Filament[]    findAll()
 * @method Filament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Filament::class);
    }

    // /**
    //  * @return Filament[] Returns an array of Filament objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Filament
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
