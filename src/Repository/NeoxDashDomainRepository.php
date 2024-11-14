<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NeoxDashDomain>
 */
class NeoxDashDomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeoxDashDomain::class);
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

    public function findByUrl(?string $value): ?array
    {
        return match ($value) {
            "", null => [], // If value is null, return an empty array
            default => $this->createQueryBuilder('n')
                            ->andWhere('n.url LIKE :val OR n.name LIKE :val') // Adds a filter on "name" with LIKE
                            ->setParameter('val', '%' . $value . '%') // Uses % to allow partial matches
                            ->getQuery()
                            ->getResult(), // Uses getResult() to fetch all matching results

        };
    }
}
