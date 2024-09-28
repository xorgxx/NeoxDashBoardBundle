<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NeoxDashSection>
 */
class NeoxDashSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NeoxDashSection::class);
    }

    //    /**
    //     * @return NeoxDashSection[] Returns an array of NeoxDashSection objects
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

    //    public function findOneBySomeField($value): ?NeoxDashSection
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function toggleFieldValue($id)
    {
        return $this->createQueryBuilder('n')
            ->update(NeoxDashSection::class, 'n')  // Mise à jour de l'entité NeoxDashSection
            ->set('n.edit', 'NOT n.edit')  // Inverse la valeur du champ 'edit'
            ->where('n.id = :id')  // Filtre pour l'ID donné
            ->setParameter('id', $id)  // Définir le paramètre 'id' pour la requête
            ->getQuery()  // Générer la requête
            ->execute();  // Exécuter la requête
    }
}
