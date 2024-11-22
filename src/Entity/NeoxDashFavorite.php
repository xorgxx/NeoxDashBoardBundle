<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashFavoriteRepository;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Gedmo\Mapping\Annotation as Gedmo;

#[Broadcast(template: '@NeoxDashBoardBundle\broadcast\NeoxDashFavorite.stream.html.twig')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NeoxDashFavoriteRepository::class)]
class NeoxDashFavorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, NeoxDashDomain>
     */
    #[ORM\OneToMany(targetEntity: NeoxDashDomain::class, mappedBy: 'favorite', orphanRemoval: true)]
    private Collection $neoxDashDomains;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

    #[ORM\Column(nullable: true)]
    private ?bool $favorite = false;

//    #[ORM\Column(length: 255, nullable: true)]
//    private ?string $name = null;

    public function __construct()
    {
        $this->neoxDashDomains = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(?bool $favorite): NeoxDashFavorite
    {
        $this->favorite = $favorite;
        return $this;
    }


    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): NeoxDashFavorite
    {
        $this->position = $position;
        return $this;
    }


    /**
     * @return Collection<int, NeoxDashDomain>
     */
    public function getNeoxDashDomains(): Collection
    {
        return $this->neoxDashDomains;
    }

    public function addNeoxDashDomain(NeoxDashDomain $neoxDashDomain): static
    {
        if (!$this->neoxDashDomains->contains($neoxDashDomain)) {
            $this->neoxDashDomains->add($neoxDashDomain);
            $neoxDashDomain->setSection($this);
        }

        return $this;
    }

    public function removeNeoxDashDomain(NeoxDashDomain $neoxDashDomain): static
    {
        if ($this->neoxDashDomains->removeElement($neoxDashDomain)) {
            // set the owning side to null (unless already changed)
            if ($neoxDashDomain->getSection() === $this) {
                $neoxDashDomain->setSection(null);
            }
        }

        return $this;
    }

}
