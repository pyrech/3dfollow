<?php

namespace App\Repository;

use App\Entity\PrintRequest;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
     * @return PrintRequest[]
     */
    public function findAllForUser(User $user): array
    {
        $qb = $this->createQueryBuilderForUser($user)
            ->orderBy('p.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return PrintRequest[]
     */
    public function findLatestPendingForUser(User $user, int $limit = 5): array
    {
        $qb = $this->createQueryBuilderForUser($user)
            ->andWhere('p.isPrinted = false')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return PrintRequest[]
     */
    public function findAllForTeam(Team $team): array
    {
        $qb = $this->createQueryBuilderForTeam($team)
            ->orderBy('p.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return PrintRequest[]
     */
    public function findLatestPendingForTeam(Team $team, int $limit = 5): array
    {
        $qb = $this->createQueryBuilderForTeam($team)
            ->andWhere('p.isPrinted = false')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
        ;

        return $qb->getQuery()->getResult();
    }

    private function createQueryBuilderForUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
        ;
    }

    private function createQueryBuilderForTeam(Team $team): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.team = :team')
            ->setParameter('team', $team)
        ;
    }
}
