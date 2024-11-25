<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Gedmo\Mapping\Annotation as Gedmo;

#[Broadcast(template: '@NeoxDashBoardBundle\broadcast\NeoxDashWidget.stream.html.twig')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NeoxDashDomainRepository::class)]
class NeoxDashWidget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\ManyToOne( inversedBy: 'neoxDashDomains' )]
    #[ORM\JoinColumn(nullable: true)]
    private ?NeoxDashSection $section = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $hash = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }


    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): NeoxDashWidget
    {
        $this->color = $color;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSection(): ?NeoxDashSection
    {
        return $this->section;
    }

    public function setSection(?NeoxDashSection $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): NeoxDashWidget
    {
        $this->hash = $hash;
        return $this;
    }
}
