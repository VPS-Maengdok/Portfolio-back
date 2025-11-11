<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Country
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, CountryI18n>
     */
    #[ORM\OneToMany(targetEntity: CountryI18n::class, mappedBy: 'country', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    /**
     * @var Collection<int, School>
     */
    #[ORM\OneToMany(targetEntity: School::class, mappedBy: 'country')]
    private Collection $schools;

    /**
     * @var Collection<int, Company>
     */
    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'country')]
    private Collection $companies;

    #[ORM\ManyToOne(inversedBy: 'visaAvailableFor')]
    private ?Curriculum $visaAvailability = null;

    #[ORM\ManyToOne(inversedBy: 'expectedCountry')]
    private ?Curriculum $expectedCountry = null;

    public function __construct()
    {
        $this->i18n = new ArrayCollection();
        $this->schools = new ArrayCollection();
        $this->companies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CountryI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(CountryI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setCountry($this);
        }

        return $this;
    }

    public function removeI18n(CountryI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getCountry() === $this) {
                $i18n->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    public function addSchool(School $school): static
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
            $school->setCountry($this);
        }

        return $this;
    }

    public function removeSchool(School $school): static
    {
        if ($this->schools->removeElement($school)) {
            // set the owning side to null (unless already changed)
            if ($school->getCountry() === $this) {
                $school->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): static
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setCountry($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): static
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getCountry() === $this) {
                $company->setCountry(null);
            }
        }

        return $this;
    }

    public function getVisaAvailability(): ?Curriculum
    {
        return $this->visaAvailability;
    }

    public function setVisaAvailability(?Curriculum $visaAvailability): static
    {
        $this->visaAvailability = $visaAvailability;

        return $this;
    }

    public function getExpectedCountry(): ?Curriculum
    {
        return $this->expectedCountry;
    }

    public function setExpectedCountry(?Curriculum $expectedCountry): static
    {
        $this->expectedCountry = $expectedCountry;

        return $this;
    }
}
