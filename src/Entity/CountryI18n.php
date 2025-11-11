<?php

namespace App\Entity;

use App\Repository\CountryI18nRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryI18nRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CountryI18n
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
    private ?Country $country = null;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }
}
