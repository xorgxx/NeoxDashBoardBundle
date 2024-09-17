<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashDomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: NeoxDashDomainRepository::class)]
#[Broadcast]
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

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'neoxDashDomains')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NeoxDashSection $section = null;

    #[ORM\Column(length: 1)]
    private ?string $firstLetter = null;


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

    public function setSlug(string $slug): static
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

    public function getFirstLetter(): ?string
    {
        return $this->firstLetter;
    }

    public function setFirstLetter(string $firstLetter): static
    {
        $this->firstLetter = $firstLetter;

        return $this;
    }


}
