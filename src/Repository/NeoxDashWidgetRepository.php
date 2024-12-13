<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Repository;

    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
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

        /**
         * @param string $widgetName
         *
         * @return NeoxDashWidget|null Returns an array of NeoxDashWidget objects
         */
        public function findOneByPublish(string $widgetName ): ?NeoxDashWidget
        {
            $dql = "
                SELECT widget, section, class
                FROM NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashWidget widget
                JOIN widget.section section
                JOIN section.class class
                WHERE widget.widget = :widgetName
            ";

            $query = $this
                ->getEntityManager()
                ->createQuery($dql);

            $query->setParameter('widgetName', $widgetName);
//            $query->setParameter('publish', $publish);

            // Utilisez getOneOrNullResult() si vous attendez un seul résultat
            return $query->getOneOrNullResult();
        }


        public function findByWidgetGetClass(string $widgetName = 'Favorite'): ?NeoxDashClass
        {
            $dql = "
                SELECT DISTINCT class
                FROM NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass class
                JOIN class.neoxDashSections section
                JOIN section.neoxDashWidgets widget
                WHERE widget.widget = :widget
            ";

            $query = $this->getEntityManager()->createQuery($dql);
            $query->setParameter('widget', $widgetName);

            return $query->getOneOrNullResult();

        }

        /**
         * Example method to add a widget entity to the database.
         *
         * @param NeoxDashWidget $entity
         * @param bool           $flush
         */
        public function add(NeoxDashWidget $entity, bool $flush = false): void
        {
            $this
                ->getEntityManager()
                ->persist($entity)
            ;

            if ($flush) {
                $this
                    ->getEntityManager()
                    ->flush()
                ;
            }
        }

        /**
         * Example method to remove a widget entity from the database.
         *
         * @param NeoxDashWidget $entity
         * @param bool           $flush
         */
        public function remove(NeoxDashWidget $entity, bool $flush = false): void
        {
            $this
                ->getEntityManager()
                ->remove($entity)
            ;

            if ($flush) {
                $this
                    ->getEntityManager()
                    ->flush()
                ;
            }
        }
    }
