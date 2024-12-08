<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashSetup;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxDashTypeEnum;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSizeEnum;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxStyleEnum;
use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: NeoxDashClassRepository::class)]
class NeoxDashClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: NeoxDashTypeEnum::class)]
    private NeoxDashTypeEnum $type;

    /**
     * @var Collection<int, NeoxDashSection>
     */
    #[ORM\OneToMany(targetEntity: NeoxDashSection::class, mappedBy: 'class', cascade: [ 'persist', 'remove'], orphanRemoval: true)]
    private Collection $neoxDashSections;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(length: 100, nullable: true, enumType: NeoxStyleEnum::class)]
    private ?NeoxStyleEnum $mode = null;

    #[ORM\ManyToOne(inversedBy: 'class')]
    private ?NeoxDashSetup $neoxDashSetup = null;

    #[ORM\Column(type: 'integer')]
    #[Gedmo\SortablePosition()]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headerColor = null;

    #[ORM\Column(length: 100, nullable: true, enumType: NeoxSizeEnum::class)]
    private ?NeoxSizeEnum $size = NeoxSizeEnum::COL3;

    #[ORM\Column(nullable: true)]
    private ?bool $publish = true;

    public function __construct()
    {
        $this->neoxDashSections = new ArrayCollection();
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

    public function getType(): NeoxDashTypeEnum
    {
        return $this->type;
    }

    public function setType(NeoxDashTypeEnum $type): NeoxDashClass
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<int, NeoxDashSection>
     */
    public function getNeoxDashSections(): Collection
    {
        return $this->neoxDashSections;
    }

    public function addNeoxDashSection(NeoxDashSection $neoxDashSection): static
    {
        if (!$this->neoxDashSections->contains($neoxDashSection)) {
            $this->neoxDashSections->add($neoxDashSection);
            $neoxDashSection->setClass($this);
        }

        return $this;
    }

    public function removeNeoxDashSection(NeoxDashSection $neoxDashSection): static
    {
        if ($this->neoxDashSections->removeElement($neoxDashSection)) {
            // set the owning side to null (unless already changed)
            if ($neoxDashSection->getClass() === $this) {
                $neoxDashSection->setClass(null);
            }
        }

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getMode(): ?NeoxStyleEnum
    {
        return $this->mode;
    }

    public function setMode(?NeoxStyleEnum $mode): NeoxDashClass
    {
        $this->mode = $mode;
        return $this;
    }

    public function getNeoxDashSetup(): ?NeoxDashSetup
    {
        return $this->neoxDashSetup;
    }

    public function setNeoxDashSetup(?NeoxDashSetup $neoxDashSetup): static
    {
        $this->neoxDashSetup = $neoxDashSetup;

        return $this;
    }

    public function getHeaderColor(): ?string
    {
        return $this->headerColor;
    }

    public function setHeaderColor(?string $headerColor): NeoxDashClass
    {
        $this->headerColor = $headerColor;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): NeoxDashClass
    {
        $this->position = $position;
        return $this;
    }

    public function getSize(): ?NeoxSizeEnum
    {
        return $this->size;
    }

    public function setSize(?NeoxSizeEnum $size): NeoxDashClass
    {
        $this->size = $size;
        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(?bool $publish): NeoxDashClass
    {
        $this->publish = $publish;
        return $this;
    }

}
