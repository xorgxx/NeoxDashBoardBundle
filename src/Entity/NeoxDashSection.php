<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: NeoxDashSectionRepository::class)]
class NeoxDashSection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $row = 3;

    #[ORM\Column(nullable: true)]
    private ?int $colonne = 5;

    #[ORM\Column(type: 'decimal', precision: 4, scale: 2, nullable: true)]
    private ?string $heigth = "2.8";

    #[ORM\Column(nullable: true)]
    private ?bool $edit = false;

    /**
     * @var Collection<int, NeoxDashDomain>
     */
    #[ORM\OneToMany(targetEntity: NeoxDashDomain::class, mappedBy: 'section', orphanRemoval: true)]
    private Collection $neoxDashDomains;

    #[ORM\ManyToOne(inversedBy: 'neoxDashSections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\SortableGroup()]
    private ?NeoxDashClass $class = null;

    #[ORM\Column(nullable: true)]
    private ?int $timer = null;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

    public function __construct()
    {
        $this->neoxDashDomains = new ArrayCollection();
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

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function setRow(int $row): static
    {
        $this->row = $row;

        return $this;
    }

    public function getColonne(): ?int
    {
        return $this->colonne;
    }

    public function setColonne(int $colonne): static
    {
        $this->colonne = $colonne;

        return $this;
    }

    public function getHeigth(): string
    {
        return $this->heigth;
    }

    public function setHeigth(string $heigth): NeoxDashSection
    {
        $this->heigth = $heigth;
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

    public function getClass(): ?NeoxDashClass
    {
        return $this->class;
    }

    public function setClass(?NeoxDashClass $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function getTimer(): ?int
    {
        return $this->timer;
    }

    public function setTimer(?int $timer): static
    {
        $this->timer = $timer;

        return $this;
    }

    public function getEdit(): ?bool
    {
        return $this->edit;
    }

    public function setEdit(?bool $edit): static
    {
        $this->edit = $edit;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): NeoxDashSection
    {
        $this->position = $position;
        return $this;
    }

}
