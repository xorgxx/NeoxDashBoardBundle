<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\EventSubscriber;

    use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
    use Doctrine\ORM\Event\PostLoadEventArgs;
    use Doctrine\ORM\Event\OnFlushEventArgs;
    use Doctrine\ORM\Event\PostFlushEventArgs;
    use Doctrine\ORM\Event\PostUpdateEventArgs;
    use Doctrine\ORM\Event\PrePersistEventArgs;
    use Doctrine\ORM\Events;
    use Doctrine\Persistence\Event\LifecycleEventArgs;
    use JsonException;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Message\NeoxDashDomainMessage;
    use ReflectionException;
    use Symfony\Component\Messenger\Exception\ExceptionInterface;
    use Symfony\Component\Messenger\MessageBusInterface;

    /**
     * Doctrine event subscriber which encrypt/decrypt entities
     */
//    #[AsDoctrineListener( event: Events::onFlush, priority: 500, connection: 'default' )]
//    #[AsDoctrineListener( event: Events::postLoad, priority: 500, connection: 'default' )]
//    #[AsDoctrineListener( event: Events::postFlush, priority: 500, connection: 'default' )]
    #[AsDoctrineListener( event: Events::postUpdate, priority: 500, connection: 'default' )]
    #[AsDoctrineListener( event: Events::prePersist, priority: 500, connection: 'default' )]
    class NeoxDashDomainSubscriber
    {

        public function __construct( readonly private MessageBusInterface $bus){}


        // callback methods must be called exactly like the events they listen to;
        // they receive an argument of type LifecycleEventArgs, which gives you access
        // to both the entity object of the event and the entity manager itself
        public function prePersist(LifecycleEventArgs $args): void
        {
            $this->logActivity('persist', $args);
        }

//        public function prePersist(LifecycleEventArgs $args): void
//        {
//            $this->logActivity('prePersist', $args);
//        }

//        public function postRemove(LifecycleEventArgs $args): void
//        {
//            $this->logActivity('remove', $args);
//        }

        public function postUpdate(LifecycleEventArgs $args): void
        {
            $this->logActivity('update', $args);
        }

//        public function postLoad(LifecycleEventArgs $args): void
//        {
//            $this->logActivity('update', $args);
//        }


        /**
         * @throws ExceptionInterface
         */
        private function logActivity(string $action, LifecycleEventArgs $args): void
        {
            $entity = $args->getObject();
            $em = $args->getObjectManager();
//            $uow    = $em->getUnitOfWork();

            // if this subscriber only applies to certain entity types,
            // add some code to check the entity type as early as possible
            if ($entity instanceof NeoxDashDomain) {

                switch ($action) {
                    case "update":
                    case "prePersist":
                        $this->bus->dispatch(new NeoxDashDomainMessage($entity->getId()));
                        break;

//                        $this->bus->dispatch(new NeoxDashDomainMessage($entity->getId()));
//                        break;
//            case "remove":
//
//                break;
//            case 'persist':
//
//                break;
                }
            }
        }
    }