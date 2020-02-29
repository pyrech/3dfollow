<?php

namespace App\Repository;

use App\Entity\PrintObject;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    /**
     * @return PrintObject[]
     */
    public function findAllForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
        ;

        return $qb->getQuery()->getResult();
    }
}
