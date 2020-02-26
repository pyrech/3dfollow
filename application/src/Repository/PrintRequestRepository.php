<?php

namespace App\Repository;

use App\Entity\Filament;
use App\Entity\PrintRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PrintRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintRequest[]    findAll()
 * @method PrintRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintRequest::class);
    }

    /**
     * @return Filament[]
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
