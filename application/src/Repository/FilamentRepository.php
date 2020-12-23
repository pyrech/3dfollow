<?php

namespace App\Repository;

use App\Entity\Filament;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @return Filament[]
     */
    public function findAllForOwner(User $user): array
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.owner = :user')
            ->setParameter('user', $user)
        ;

        return $qb->getQuery()->getResult();
    }
}
