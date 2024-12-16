<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use Doctrine\ORM\QueryBuilder;
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

    public function findByDomainDateTime(string $filter, string $order = 'DESC'): ?array
    {
        $queryBuilder = $this->createQueryBuilder("d");
        $queryBuilder->orderBy('d.updatedAt', $order);
        if (str_starts_with($filter, '@')) {
            [$type, $value] = explode(':', substr($filter, 1)) + [null, 0];
            $value          = (int)$value;

            if ($value > 0) {
                $currentDate = new \DateTime();

                switch ($type) {
                    case 'hours':
                        $this->addRangeCondition(
                            $queryBuilder,
                            'd.updatedAt',
                            'HOUR',
                            max(0, (int)$currentDate->format('H') - $value),
                            (int)$currentDate->format('H')
                        );
                        break;
                    case 'days':
                        $this->addDateRangeCondition(
                            $queryBuilder,
                            'd.updatedAt',
                            (clone $currentDate)->modify("-{$value} days"),
                            $currentDate
                        );
                        break;
                    case 'weeks':
                        $this->addDateRangeCondition(
                            $queryBuilder,
                            'd.updatedAt',
                            (clone $currentDate)->modify("-{$value} weeks"),
                            $currentDate
                        );
                        break;
                    default:
                        return [];
                }
            }else{
                return [];
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Ajoute une condition de plage horaire au QueryBuilder.
     */
    private function addRangeCondition($queryBuilder, string $field, string $unit, int $start, int $end): void
    {
        $queryBuilder->andWhere("{$unit}({$field}) BETWEEN :start AND :end")
                     ->setParameter('start', $start)
                     ->setParameter('end', $end);
    }

    /**
     * Ajoute une condition de plage de dates au QueryBuilder.
     */
    private function addDateRangeCondition($queryBuilder, string $field, \DateTime $startDate, \DateTime $endDate): void
    {
        $queryBuilder->andWhere("{$field} BETWEEN :startDate AND :endDate")
                     ->setParameter('startDate', $startDate)
                     ->setParameter('endDate', $endDate);
    }


}
