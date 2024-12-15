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
#[ORM\HasLifecycleCallbacks]
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


    #[ORM\ManyToOne(fetch: "EAGER", inversedBy: 'neoxDashDomains')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\SortableGroup()]
    private ?NeoxDashSection $section = null;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

    #[ORM\ManyToOne(cascade: [ 'persist', 'remove'], inversedBy: 'neoxDashDomains')]
    #[ORM\JoinColumn(nullable: true)]
    private ?NeoxDashFavorite $favorite = null;

    #[ORM\Column(type: 'integer')]
    private ?int $cpt = 0;

//    /**
//     * @var Collection<int, NeoxDashDomainHistory>
//     */
//    #[ORM\OneToMany(targetEntity: NeoxDashDomainHistory::class, mappedBy: 'domain', orphanRemoval: true)]
//    private Collection $neoxDashDomainsHistory;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $hash = null;

    public function __construct()
    {
//        $this->neoxDashDomainsHistory = new ArrayCollection();
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

    public function getFavorite(): ?NeoxDashFavorite
    {
        return $this->favorite;
    }

    public function setFavorite(?NeoxDashFavorite $favorite): static
    {
        $this->favorite = $favorite;

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

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): NeoxDashDomain
    {
        $this->hash = $hash;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): NeoxDashDomain
    {
        $this->content = $content;
        return $this;
    }

    public function getCpt(): ?int
    {
        return $this->cpt;
    }

    public function setCpt(?int $cpt): NeoxDashDomain
    {
        $this->cpt = $cpt;
        return $this;
    }
//    public function getNeoxDashDomainsHistory(): ArrayCollection
//    {
//        return $this->neoxDashDomainsHistory;
//    }
//
//    public function setNeoxDashDomainsHistory(ArrayCollection $neoxDashDomainsHistory): NeoxDashDomain
//    {
//        $this->neoxDashDomainsHistory = $neoxDashDomainsHistory;
//        return $this;
//    }



//    /**
//     * @return Collection<int, NeoxDashDomain>
//     */
//    public function getNeoxDashDomainsHistory(): Collection
//    {
//        return $this->neoxDashDomainsHistory;
//    }
//
//    public function addNeoxDashDomainHistory(NeoxDashDomainHistory $neoxDashDomainHistory): static
//    {
//        if (!$this->neoxDashDomainsHistory->contains($neoxDashDomainHistory)) {
//            $this->neoxDashDomainsHistory->add($neoxDashDomainHistory);
//            $neoxDashDomainHistory->setDomain($this);
//        }
//
//        return $this;
//    }
//
//    public function removeNeoxDashDomainHistory(NeoxDashDomainHistory $neoxDashDomainHistory): static
//    {
//        if ($this->neoxDashDomainsHistory->removeElement($neoxDashDomainHistory)) {
//            // set the owning side to null (unless already changed)
//            if ($neoxDashDomainHistory->getDomain() === $this) {
//                $neoxDashDomainHistory->setDomain(null);
//            }
//        }
//
//        return $this;
//    }



    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateHash(): void
    {
        // Concatène les attributs à hacher
        $concatenatedValue = $this->url;

        // Générez le hash et définissez-le
        $this->hash = hash('sha256', $concatenatedValue);
    }

}
