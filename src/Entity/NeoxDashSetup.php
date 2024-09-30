<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use NeoxDashBoard\NeoxDashBoardBundle\Entity\NeoxDashClass;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxSearchEnum;
use NeoxDashBoard\NeoxDashBoardBundle\Repository\NeoxDashSetupRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: NeoxDashSetupRepository::class)]
class NeoxDashSetup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[Assert\NotBlank(message:"Le nom ne doit pas être vide.")]
    #[ORM\Column(length: 5)]
    private string $country ;

    #[ORM\Column(length: 255, enumType: NeoxSearchEnum::class, nullable: true)]
    private ?NeoxSearchEnum $search = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $weather = null ;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $home = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theme = null;

    /**
     * @var Collection<int, NeoxDashClass>
     */
    #[ORM\OneToMany(targetEntity: NeoxDashClass::class, mappedBy: 'neoxDashSetup')]
    private Collection $class;

    public function __construct()
    {
        $this->class = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(?string $weather): static
    {
        $this->weather = $weather;
        return $this;
    }

    public function getSearch(): ?NeoxSearchEnum
    {
        return $this->search;
    }

    public function setSearch(?NeoxSearchEnum $search): static
    {
        $this->search = $search;
        return $this;
    }

    public function getHome(): ?string
    {
        return $this->home;
    }

    public function setHome(?string $home): static
    {
        $this->home = $home;
        return $this;
    }

    

    /**
     * @return Collection<int, NeoxDashClass>
     */
    public function getClass(): Collection
    {
        return $this->class;
    }

    public function addClass(NeoxDashClass $class): static
    {
        if (!$this->class->contains($class)) {
            $this->class->add($class);
            $class->setNeoxDashSetup($this);
        }

        return $this;
    }

    public function removeClass(NeoxDashClass $class): static
    {
        if ($this->class->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getNeoxDashSetup() === $this) {
                $class->setNeoxDashSetup(null);
            }
        }

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): NeoxDashSetup
    {
        $this->theme = $theme;
        return $this;
    }
    
}
