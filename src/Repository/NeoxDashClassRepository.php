<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NeoxDashType>
 */
class NeoxDashClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeoxDashClass::class);
    }

//    /**
//     * @return NeoxDashType[] Returns an array of NeoxDashType objects
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

    public function findOneClass($classId): ?NeoxDashClass
    {
        $qb = $this->createQueryBuilder('c') // Point de départ sur NeoxDashClass
                   ->select('c', 's', 'd', 'setup') // Sélectionner les entités que vous voulez récupérer
                   ->innerJoin('c.neoxDashSections', 's') // Joindre les NeoxDashSections associées à NeoxDashClass
                   ->innerJoin('s.neoxDashDomains', 'd') // Joindre les NeoxDashDomains associées à NeoxDashSection
                   ->innerJoin('c.neoxDashSetup', 'setup') // Joindre NeoxDashSetup associée à NeoxDashClass
                   ->where('c.id = :classId') // Filtrer par ID de NeoxDashClass
                   ->setParameter('classId', $classId) // Passer la valeur de classId
                   ->orderBy('s.position', 'ASC') // Trier par section.position en premier
                   ->addOrderBy('d.position', 'ASC') // Trier par domain.position en second
;
        // Exécuter la requête et retourner une seule entité ou null si aucun résultat
        return $qb->getQuery()->getOneOrNullResult();
    }

}
