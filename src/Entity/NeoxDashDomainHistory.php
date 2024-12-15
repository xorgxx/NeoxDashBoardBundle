<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\Traits\TimeStampable;
use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;


//#[Broadcast(template: '@NeoxDashBoardBundle\broadcast\NeoxDashDomain.stream.html.twig')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NeoxDashDomainHistoryRepository::class)]
class NeoxDashDomainHistory
{
    use TimeStampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $count = null;

    #[ORM\ManyToOne(fetch: "EAGER", inversedBy: 'neoxDashDomainsHistory')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NeoxDashDomain $domain = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): NeoxDashDomainHistory
    {
        $this->count = $count;
        return $this;
    }

    public function getDomain(): ?NeoxDashDomain
    {
        return $this->domain;
    }

    public function setDomain(?NeoxDashDomain $domain): NeoxDashDomainHistory
    {
        $this->domain = $domain;
        return $this;
    }

}
