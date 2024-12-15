<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomainHistory;

/**
 * @extends ServiceEntityRepository<NeoxDashDomainHistory>
 */
class NeoxDashDomainHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeoxDashDomainHistory::class);
    }

    //    /**
    //     * @return NeoxDashDomain[] Returns an array of NeoxDashDomain objects
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
