<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashFavorite;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<NeoxDashFavorite>
     */
    class NeoxDashFavoriteRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, NeoxDashFavorite::class);
        }

        //    /**
        //     * @return NeoxDashFavorite[] Returns an array of NeoxDashFavorite objects
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

        public function findFavorites(): array
        {
            return $this
                ->createQueryBuilder('n')
                ->andWhere('n.favorite = :favorite') // Filtre sur la colonne "favorite"
                ->setParameter('favorite', true) // Seules les entités avec favorite=true
                ->orderBy('n.position', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

        public function findOnlyFavorites()
        {
            return $this
                ->createQueryBuilder('f') // Alias for NeoxDashFavorite
                ->select('f, d, s, c')   // Select all entities involved
                ->innerJoin('f.neoxDashDomains', 'd') // Join NeoxDashDomain
                ->innerJoin('d.section', 's')         // Join NeoxDashSection
                ->innerJoin('s.class', 'c')           // Join NeoxDashClass
                ->where('f.favorite = :isFavorite')   // Filter by favorite
                ->setParameter('isFavorite', true)    // Bind parameter
                ->orderBy('f.position', 'ASC')
                ->getQuery()
                ->getResult();
        }




    }
