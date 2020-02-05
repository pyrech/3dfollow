<?php

namespace App\Repository;

use App\Entity\PrintItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PrintItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintItem[]    findAll()
 * @method PrintItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrintItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintItem::class);
    }
}
