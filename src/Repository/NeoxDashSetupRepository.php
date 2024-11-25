<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<NeoxDashSetup>
     */
    class NeoxDashSetupRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, NeoxDashSetup::class);
        }

        //    /**
        //     * @return NeoxDashSetup[] Returns an array of NeoxDashSetup objects
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

        public function findOneSetup($setupId): ?NeoxDashSetup
        {
            $qb = $this
                ->createQueryBuilder('setup') // Point de départ sur NeoxDashSetup
                ->select('setup', 'c', 's', 'd') // Sélectionner les entités que vous voulez récupérer
                ->leftJoin('setup.class', 'c') // Joindre les NeoxDashClass associées à NeoxDashSetup
                ->leftJoin('c.neoxDashSections', 's') // Joindre les NeoxDashSections associées à NeoxDashClass
                ->leftJoin('s.neoxDashDomains', 'd') // Joindre les NeoxDashDomains associées à NeoxDashSection
                ->where('setup.id = :setupId') // Filtrer par ID de NeoxDashSetup
                ->setParameter('setupId', $setupId) // Passer la valeur de setupId
                ->orderBy('c.position', 'ASC') // Trier les sections par la position
                ->addOrderBy('s.position', 'ASC') // Trier les sections par la position
                ->addOrderBy('d.position', 'ASC')
            ; // Trier par domain.position en second

            // Exécuter la requête et retourner les résultats
            return $qb
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

//        public function findOneSetup($setupId): ?NeoxDashSetup
//        {
//            $qb = $this->createQueryBuilder('setup') // Point de départ sur NeoxDashSetup
//                ->select('setup', 'c', 's', 'd') // Sélectionner les entités que vous voulez récupérer
//                ->innerJoin('setup.class', 'c') // Joindre les NeoxDashClass associées à NeoxDashSetup
//                ->innerJoin('c.neoxDashSections', 's') // Joindre les NeoxDashSections associées à NeoxDashClass
//                ->innerJoin('s.neoxDashDomains', 'd') // Joindre les NeoxDashDomains associées à NeoxDashSection
//                ->where('setup.id = :setupId') // Filtrer par ID de NeoxDashSetup
//                ->setParameter('setupId', $setupId) // Passer la valeur de setupId
//                ->orderBy('s.position', 'ASC') // Trier les sections par la position
//                ->addOrderBy('d.position', 'ASC'); // Trier par domain.position en second
//            ;
//            // Exécuter la requête et retourner les résultats
//            return $qb
//                ->getQuery()
//                ->getOneOrNullResult()
//            ;
//        }
    }
