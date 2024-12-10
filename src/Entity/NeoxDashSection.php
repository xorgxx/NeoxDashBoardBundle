<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSizeEnum;
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
    private ?int $row = 4;

    #[ORM\Column(length: 100, nullable: true, enumType: NeoxSizeEnum::class)]
    private ?NeoxSizeEnum $size = null;

    #[ORM\Column( nullable: true)]
    private ?int $count = 0;

    #[ORM\Column(nullable: true)]
    private ?bool $content = false;

    #[ORM\Column(nullable: true)]
    private ?bool $edit = false;

    /**
     * @var Collection<int, NeoxDashDomain>
     */
    #[ORM\OneToMany(targetEntity: NeoxDashDomain::class, mappedBy: 'section', orphanRemoval: true)]
    private Collection $neoxDashDomains;

    /**
     * @var Collection<int, NeoxDashWidget> | null
     */
    #[ORM\OneToMany(targetEntity: NeoxDashWidget::class, mappedBy: 'section', orphanRemoval: true)]
    private ?Collection $neoxDashWidgets = null;

    #[ORM\ManyToOne(inversedBy: 'neoxDashSections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\SortableGroup()]
    private ?NeoxDashClass $class = null;

    #[ORM\Column(nullable: true)]
    private ?int $timer = null;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headerColor = null;

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

    public function getSize(): ?NeoxSizeEnum
    {
        return $this->size;
    }

    public function setSize(?NeoxSizeEnum $size): NeoxDashSection
    {
        $this->size = $size;
        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): NeoxDashSection
    {
        $this->count = $count;
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

    /**
     * @return Collection<int, NeoxDashWidget>
     */
    public function getNeoxDashWidgets(): Collection
    {
        if ($this->neoxDashWidgets === null) {
            $this->neoxDashWidgets = new ArrayCollection();
        }

        return $this->neoxDashWidgets;
    }

    public function addNeoxDashWidget(NeoxDashWidget $neoxDashWidget): static
    {
        if (!$this->neoxDashWidgets->contains($neoxDashWidget)) {
            $this->neoxDashWidgets->add($neoxDashWidget);
            $neoxDashWidget->setSection($this);
        }

        return $this;
    }

    public function removeNeoxDashWidget(NeoxDashWidget $neoxDashWidget): static
    {
        if ($this->neoxDashWidgets->removeElement($neoxDashWidget)) {
            // set the owning side to null (unless already changed)
            if ($neoxDashWidget->getSection() === $this) {
                $neoxDashWidget->setSection(null);
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

    public function getContent(): ?bool
    {
        return $this->content;
    }

    public function setContent(?bool $content): NeoxDashSection
    {
        $this->content = $content;
        return $this;
    }

    public function getHeaderColor(): ?string
    {
        return $this->headerColor;
    }

    public function setHeaderColor(?string $headerColor): NeoxDashSection
    {
        $this->headerColor = $headerColor;
        return $this;
    }




}
