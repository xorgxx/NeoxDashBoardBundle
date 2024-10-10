<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\MessageHandler;

    use Doctrine\ORM\EntityManagerInterface;
    use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashDomain;
    use NeoxDashBoard\NeoxDashBoardBundle\Services\FindIconOnWebSite;
    use Symfony\Component\Messenger\Attribute\AsMessageHandler;
    use NeoxDashBoard\NeoxDashBoardBundle\Message\NeoxDashDomainMessage;

    #[AsMessageHandler]
    class NeoxDashDomainHandler
    {

        /**
         *
         *
         * @param EntityManagerInterface $entityManager
         */
        public function __construct(readonly private EntityManagerInterface $entityManager, readonly private FindIconOnWebSite $findIconOnWebSite)
        {
        }

        
        public function __invoke(NeoxDashDomainMessage $message): void
        {
            // get domain from message
            if ($entity = $this->entityManager->getRepository(NeoxDashDomain::class)->findOneBy([ 'id' => $message->getDomainId() ])) {
                $iconPath = $this->findIconOnWebSite->getFaviconUrl($entity->getUrl());
                $entity->seturlIcon($iconPath);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }

        }
    }