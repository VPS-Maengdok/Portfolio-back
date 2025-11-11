<?php

namespace App\Entity;

use App\Repository\LocaleRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocaleRepository::class)]
class Locale
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $shortened = null;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: TagI18n::class)]
    private Collection $tagI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: StatusI18n::class)]
    private Collection $statusI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: SkillI18n::class)]
    private Collection $skillI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: WorkTypeI18n::class)]
    private Collection $workTypeI18n;

    /** @var Collection<int, CountryI18n> */
    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: CountryI18n::class)]
    private Collection $countryI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: LinkI18n::class)]
    private Collection $linkI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: CurriculumI18n::class)]
    private Collection $curriculumI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: LanguageI18n::class)]
    private Collection $languageI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: ExperienceI18n::class)]
    private Collection $experienceI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: EducationI18n::class)]
    private Collection $educationI18n;

    #[ORM\OneToMany(mappedBy: 'locale', targetEntity: ProjectI18n::class)]
    private Collection $projectI18n;

    public function __construct()
    {
        $this->tagI18ns = new ArrayCollection();
        $this->statusI18ns = new ArrayCollection();
        $this->skillI18ns = new ArrayCollection();
        $this->workTypeI18ns = new ArrayCollection();
        $this->countryI18ns = new ArrayCollection();
        $this->linkI18ns = new ArrayCollection();
        $this->curriculumI18ns = new ArrayCollection();
        $this->languageI18ns = new ArrayCollection();
        $this->experienceI18ns = new ArrayCollection();
        $this->educationI18ns = new ArrayCollection();
        $this->projectI18ns = new ArrayCollection();
    }

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

    public function getShortened(): ?string
    {
        return $this->shortened;
    }

    public function setShortened(string $shortened): static
    {
        $this->shortened = $shortened;

        return $this;
    }

    /**
     * @return Collection<int, TagI18n>
     */
    public function getTagI18ns(): Collection
    {
        return $this->tagI18ns;
    }

    public function addTagI18n(tagI18n $tagI18n): static
    {
        if (!$this->tagI18ns->contains($tagI18n)) {
            $this->tagI18ns->add($tagI18n);
            $tagI18n->setLocale($this);
        }
        return $this;
    }

    public function removeTagI18n(tagI18n $tagI18n): static
    {
        if ($this->tagI18ns->removeElement($tagI18n)) {
            if ($tagI18n->getLocale() === $this) {
                $tagI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, statusI18n>
     */
    public function getStatusI18ns(): Collection
    {
        return $this->statusI18ns;
    }

    public function addStatusI18n(StatusI18n $statusI18n): static
    {
        if (!$this->statusI18ns->contains($statusI18n)) {
            $this->statusI18ns->add($statusI18n);
            $statusI18n->setLocale($this);
        }
        return $this;
    }

    public function removeStatusI18n(StatusI18n $statusI18n): static
    {
        if ($this->statusI18ns->removeElement($statusI18n)) {
            if ($statusI18n->getLocale() === $this) {
                $statusI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, SkillI18n>
     */
    public function getSkillI18ns(): Collection
    {
        return $this->skillI18ns;
    }

    public function addSkillI18n(SkillI18n $skillI18n): static
    {
        if (!$this->skillI18ns->contains($skillI18n)) {
            $this->skillI18ns->add($skillI18n);
            $skillI18n->setLocale($this);
        }
        return $this;
    }

    public function removeSkillI18n(SkillI18n $skillI18n): static
    {
        if ($this->skillI18ns->removeElement($skillI18n)) {
            if ($skillI18n->getLocale() === $this) {
                $skillI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, WorkTypeI18n>
     */
    public function getWorkTypeI18ns(): Collection
    {
        return $this->workTypeI18ns;
    }

    public function addWorkTypeI18n(WorkTypeI18n $workTypeI18n): static
    {
        if (!$this->workTypeI18ns->contains($workTypeI18n)) {
            $this->workTypeI18ns->add($workTypeI18n);
            $workTypeI18n->setLocale($this);
        }
        return $this;
    }

    public function removeWorkTypeI18n(WorkTypeI18n $workTypeI18n): static
    {
        if ($this->workTypeI18ns->removeElement($workTypeI18n)) {
            if ($workTypeI18n->getLocale() === $this) {
                $workTypeI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CountryI18n>
     */
    public function getCountryI18ns(): Collection
    {
        return $this->countryI18ns;
    }

    public function addCountryI18n(CountryI18n $countryI18n): static
    {
        if (!$this->countryI18ns->contains($countryI18n)) {
            $this->countryI18ns->add($countryI18n);
            $countryI18n->setLocale($this);
        }
        return $this;
    }

    public function removeCountryI18n(CountryI18n $countryI18n): static
    {
        if ($this->countryI18ns->removeElement($countryI18n)) {
            if ($countryI18n->getLocale() === $this) {
                $countryI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, LinkI18n>
     */
    public function getLinkI18ns(): Collection
    {
        return $this->linkI18ns;
    }

    public function addLinkI18n(LinkI18n $linkI18n): static
    {
        if (!$this->linkI18ns->contains($linkI18n)) {
            $this->linkI18ns->add($linkI18n);
            $linkI18n->setLocale($this);
        }
        return $this;
    }

    public function removelinkI18n(LinkI18n $linkI18n): static
    {
        if ($this->linkI18ns->removeElement($linkI18n)) {
            if ($linkI18n->getLocale() === $this) {
                $linkI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CurriculumI18n>
     */
    public function getCurriculumI18ns(): Collection
    {
        return $this->curriculumI18ns;
    }

    public function addCurriculumI18n(CurriculumI18n $curriculumI18n): static
    {
        if (!$this->curriculumI18ns->contains($curriculumI18n)) {
            $this->curriculumI18ns->add($curriculumI18n);
            $curriculumI18n->setLocale($this);
        }
        return $this;
    }

    public function removeCurriculumI18n(CurriculumI18n $curriculumI18n): static
    {
        if ($this->curriculumI18ns->removeElement($curriculumI18n)) {
            if ($curriculumI18n->getLocale() === $this) {
                $curriculumI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, LanguageI18n>
     */
    public function getlLnguageI18ns(): Collection
    {
        return $this->languageI18ns;
    }

    public function addLanguageI18n(LanguageI18n $languageI18n): static
    {
        if (!$this->languageI18ns->contains($languageI18n)) {
            $this->languageI18ns->add($languageI18n);
            $languageI18n->setLocale($this);
        }
        return $this;
    }

    public function removeLanguageI18n(LanguageI18n $languageI18n): static
    {
        if ($this->languageI18ns->removeElement($languageI18n)) {
            if ($languageI18n->getLocale() === $this) {
                $languageI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ExperienceI18n>
     */
    public function getExperienceI18ns(): Collection
    {
        return $this->experienceI18ns;
    }

    public function addExperienceI18n(ExperienceI18n $experienceI18n): static
    {
        if (!$this->experienceI18ns->contains($experienceI18n)) {
            $this->experienceI18ns->add($experienceI18n);
            $experienceI18n->setLocale($this);
        }
        return $this;
    }

    public function removeExperienceI18n(ExperienceI18n $experienceI18n): static
    {
        if ($this->experienceI18ns->removeElement($experienceI18n)) {
            if ($experienceI18n->getLocale() === $this) {
                $experienceI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, EducationI18n>
     */
    public function getEducationI18ns(): Collection
    {
        return $this->educationI18ns;
    }

    public function addEducationI18n(EducationI18n $educationI18n): static
    {
        if (!$this->educationI18ns->contains($educationI18n)) {
            $this->educationI18ns->add($educationI18n);
            $educationI18n->setLocale($this);
        }
        return $this;
    }

    public function removeEducationI18n(EducationI18n $educationI18n): static
    {
        if ($this->educationI18ns->removeElement($educationI18n)) {
            if ($educationI18n->getLocale() === $this) {
                $educationI18n->setLocale(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, ProjectI18n>
     */
    public function getProjectI18ns(): Collection
    {
        return $this->projectI18ns;
    }

    public function addProjectI18n(ProjectI18n $projectI18n): static
    {
        if (!$this->projectI18ns->contains($projectI18n)) {
            $this->projectI18ns->add($projectI18n);
            $projectI18n->setLocale($this);
        }
        return $this;
    }

    public function removeProjectI18n(ProjectI18n $projectI18n): static
    {
        if ($this->projectI18ns->removeElement($projectI18n)) {
            if ($projectI18n->getLocale() === $this) {
                $projectI18n->setLocale(null);
            }
        }
        return $this;
    }
}
