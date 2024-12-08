<?php

namespace NeoxDashBoard\NeoxDashBoardBundle\Entity;

use Doctrine\DBAL\Types\Types;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxWidgetEnum;
use NeoxDashBoard\NeoxDashBoardBundle\Enum\NeoxWidgetTypeEnum;
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['widget'])]
    private ?string $slug = null;

    #[ORM\ManyToOne( inversedBy: 'neoxDashDomains' )]
    #[ORM\JoinColumn(nullable: true)]
    private ?NeoxDashSection $section = null;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $hash = null;

    #[ORM\Column(length: 100, nullable: false, enumType: NeoxWidgetEnum::class)]
    private NeoxWidgetEnum $widget;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    #[ORM\Column(nullable: true)]
    private ?bool $publish = true;

    // Type of widget not use eyt but we can use form typage widget to process later api ....
    #[ORM\Column(length: 100, nullable: false, enumType: NeoxWidgetTypeEnum::class)]
    private ?NeoxWidgetTypeEnum $type;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $options = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): NeoxDashWidget
    {
        $this->url = $url;
        return $this;
    }

    public function getPublish(): ?bool
    {
        return $this->publish;
    }

    public function setPublish(?bool $publish): NeoxDashWidget
    {
        $this->publish = $publish;
        return $this;
    }

    public function getWidget(): NeoxWidgetEnum
    {
        return $this->widget;
    }

    public function setWidget(NeoxWidgetEnum $widget): NeoxDashWidget
    {
        $this->widget = $widget;
        return $this;
    }

    public function getType(): ?NeoxWidgetTypeEnum
    {
        return $this->type;
    }

    public function setType(?NeoxWidgetTypeEnum $type): NeoxDashWidget
    {
        $this->type = $type;
        return $this;
    }

    public function getOptions(): ?string
    {
        return $this->options;
    }

    public function setOptions(?string $options): NeoxDashWidget
    {
        $this->options = $options;
        return $this;
    }


}
