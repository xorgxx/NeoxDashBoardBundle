<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashWidget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NeoxDashWidget>
 */
class NeoxDashWidgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeoxDashWidget::class);
    }

    //    /**
    //     * @return NeoxDashWidget[] Returns an array of NeoxDashWidget objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
}
