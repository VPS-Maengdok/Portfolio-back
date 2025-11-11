<?php

namespace App\Entity;

use App\Repository\LinkI18nRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkI18nRepository::class)]
class LinkI18n
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\ManyToOne(inversedBy: 'countryI18n')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Locale $locale = null;

    #[ORM\ManyToOne(inversedBy: 'i18n')]
    private ?Link $link = null;

    #[ORM\ManyToOne(inversedBy: 'i18n')]
    private ?Project $project = null;

    public function __construct()
    {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    public function setLocale(?Locale $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): static
    {
        $this->link = $link;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }
}
