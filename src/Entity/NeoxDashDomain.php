<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Gedmo\Mapping\Annotation as Gedmo;

#[Broadcast(template: '@NeoxDashBoardBundle\broadcast\NeoxDashDomain.stream.html.twig')]
#[ORM\Entity(repositoryClass: NeoxDashDomainRepository::class)]
class NeoxDashDomain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $urlIcon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name', 'url'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'neoxDashDomains')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\SortableGroup()]
    private ?NeoxDashSection $section = null;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrlIcon(): ?string
    {
        return $this->urlIcon;
    }

    public function setUrlIcon(?string $urlIcon): NeoxDashDomain
    {
        $this->urlIcon = $urlIcon;
        return $this;
    }


    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): NeoxDashDomain
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): NeoxDashDomain
    {
        $this->position = $position;
        return $this;
    }

}
