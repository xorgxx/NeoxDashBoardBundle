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

        /**
         * @return NeoxDashWidget[] Returns an array of NeoxDashWidget objects
         */
        public function findOneByPublish(string $widget, bool $publish = true): NeoxDashWidget
        {
            $dql = "
                    SELECT widget, section, class
                    FROM App\Entity\NeoxDashWidget widget
                    JOIN widget.section section
                    JOIN section.class class
                    WHERE class.publish = :publish
                    AND neox_dash_class.widget = :widget;
                ";

            $query = $this
                ->getEntityManager()
                ->createQuery($dql)
            ;
            $query->setParameter('widget', $widget);
            $query->setParameter('publish', $publish);
            return $query->getResult();
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
