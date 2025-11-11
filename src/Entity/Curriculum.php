<?php

namespace App\Entity;

use App\Repository\CurriculumRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurriculumRepository::class)]
class Curriculum
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?bool $isFreelance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $freelanceCompanyName = null;

    #[ORM\Column]
    private ?bool $isAvailable = null;

    #[ORM\Column]
    private ?bool $hasVisa = null;

    /**
     * @var Collection<int, Country>
     */
    #[ORM\OneToMany(targetEntity: Country::class, mappedBy: 'visaAvailability')]
    private Collection $visaAvailableFor;

    /**
     * @var Collection<int, WorkType>
     */
    #[ORM\OneToMany(targetEntity: WorkType::class, mappedBy: 'curriculum')]
    private Collection $workType;

    /**
     * @var Collection<int, Link>
     */
    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: 'curriculum')]
    private Collection $link;

    /**
     * @var Collection<int, Experience>
     */
    #[ORM\OneToMany(targetEntity: Experience::class, mappedBy: 'curriculum')]
    private Collection $experience;

    /**
     * @var Collection<int, Education>
     */
    #[ORM\OneToMany(targetEntity: Education::class, mappedBy: 'curriculum')]
    private Collection $education;

    /**
     * @var Collection<int, Technology>
     */
    #[ORM\OneToMany(targetEntity: Technology::class, mappedBy: 'curriculum')]
    private Collection $technology;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\OneToMany(targetEntity: Skill::class, mappedBy: 'curriculum')]
    private Collection $skill;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'curriculum')]
    private Collection $project;

    /**
     * @var Collection<int, Language>
     */
    #[ORM\OneToMany(targetEntity: Language::class, mappedBy: 'curriculum')]
    private Collection $language;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Country $location = null;

    /**
     * @var Collection<int, Country>
     */
    #[ORM\OneToMany(targetEntity: Country::class, mappedBy: 'expectedCountry')]
    private Collection $expectedCountry;

    /**
     * @var Collection<int, CurriculumI18n>
     */
    #[ORM\OneToMany(targetEntity: CurriculumI18n::class, mappedBy: 'curriculum', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    public function __construct()
    {
        $this->visaAvailableFor = new ArrayCollection();
        $this->workType = new ArrayCollection();
        $this->link = new ArrayCollection();
        $this->experience = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->technology = new ArrayCollection();
        $this->skill = new ArrayCollection();
        $this->project = new ArrayCollection();
        $this->language = new ArrayCollection();
        $this->expectedCountry = new ArrayCollection();
        $this->i18n = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isFreelance(): ?bool
    {
        return $this->isFreelance;
    }

    public function setIsFreelance(bool $isFreelance): static
    {
        $this->isFreelance = $isFreelance;

        return $this;
    }

    public function getFreelanceCompanyName(): ?string
    {
        return $this->freelanceCompanyName;
    }

    public function setFreelanceCompanyName(?string $freelanceCompanyName): static
    {
        $this->freelanceCompanyName = $freelanceCompanyName;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function hasVisa(): ?bool
    {
        return $this->hasVisa;
    }

    public function setHasVisa(bool $hasVisa): static
    {
        $this->hasVisa = $hasVisa;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getVisaAvailableFor(): Collection
    {
        return $this->visaAvailableFor;
    }

    public function addVisaAvailableFor(Country $visaAvailableFor): static
    {
        if (!$this->visaAvailableFor->contains($visaAvailableFor)) {
            $this->visaAvailableFor->add($visaAvailableFor);
            $visaAvailableFor->setVisaAvailability($this);
        }

        return $this;
    }

    public function removeVisaAvailableFor(Country $visaAvailableFor): static
    {
        if ($this->visaAvailableFor->removeElement($visaAvailableFor)) {
            // set the owning side to null (unless already changed)
            if ($visaAvailableFor->getVisaAvailability() === $this) {
                $visaAvailableFor->setVisaAvailability(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkType>
     */
    public function getWorkType(): Collection
    {
        return $this->workType;
    }

    public function addWorkType(WorkType $workType): static
    {
        if (!$this->workType->contains($workType)) {
            $this->workType->add($workType);
            $workType->setCurriculum($this);
        }

        return $this;
    }

    public function removeWorkType(WorkType $workType): static
    {
        if ($this->workType->removeElement($workType)) {
            // set the owning side to null (unless already changed)
            if ($workType->getCurriculum() === $this) {
                $workType->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLink(): Collection
    {
        return $this->link;
    }

    public function addLink(Link $link): static
    {
        if (!$this->link->contains($link)) {
            $this->link->add($link);
            $link->setCurriculum($this);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        if ($this->link->removeElement($link)) {
            // set the owning side to null (unless already changed)
            if ($link->getCurriculum() === $this) {
                $link->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperience(): Collection
    {
        return $this->experience;
    }

    public function addExperience(Experience $experience): static
    {
        if (!$this->experience->contains($experience)) {
            $this->experience->add($experience);
            $experience->setCurriculum($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): static
    {
        if ($this->experience->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getCurriculum() === $this) {
                $experience->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Education>
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(Education $education): static
    {
        if (!$this->education->contains($education)) {
            $this->education->add($education);
            $education->setCurriculum($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): static
    {
        if ($this->education->removeElement($education)) {
            // set the owning side to null (unless already changed)
            if ($education->getCurriculum() === $this) {
                $education->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getTechnology(): Collection
    {
        return $this->technology;
    }

    public function addTechnology(Technology $technology): static
    {
        if (!$this->technology->contains($technology)) {
            $this->technology->add($technology);
            $technology->setCurriculum($this);
        }

        return $this;
    }

    public function removeTechnology(Technology $technology): static
    {
        if ($this->technology->removeElement($technology)) {
            // set the owning side to null (unless already changed)
            if ($technology->getCurriculum() === $this) {
                $technology->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkill(): Collection
    {
        return $this->skill;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skill->contains($skill)) {
            $this->skill->add($skill);
            $skill->setCurriculum($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        if ($this->skill->removeElement($skill)) {
            // set the owning side to null (unless already changed)
            if ($skill->getCurriculum() === $this) {
                $skill->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): static
    {
        if (!$this->project->contains($project)) {
            $this->project->add($project);
            $project->setCurriculum($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->project->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getCurriculum() === $this) {
                $project->setCurriculum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguage(): Collection
    {
        return $this->language;
    }

    public function addLanguage(Language $language): static
    {
        if (!$this->language->contains($language)) {
            $this->language->add($language);
            $language->setCurriculum($this);
        }

        return $this;
    }

    public function removeLanguage(Language $language): static
    {
        if ($this->language->removeElement($language)) {
            // set the owning side to null (unless already changed)
            if ($language->getCurriculum() === $this) {
                $language->setCurriculum(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?Country
    {
        return $this->location;
    }

    public function setLocation(?Country $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getExpectedCountry(): Collection
    {
        return $this->expectedCountry;
    }

    public function addExpectedCountry(Country $expectedCountry): static
    {
        if (!$this->expectedCountry->contains($expectedCountry)) {
            $this->expectedCountry->add($expectedCountry);
            $expectedCountry->setExpectedCountry($this);
        }

        return $this;
    }

    public function removeExpectedCountry(Country $expectedCountry): static
    {
        if ($this->expectedCountry->removeElement($expectedCountry)) {
            // set the owning side to null (unless already changed)
            if ($expectedCountry->getExpectedCountry() === $this) {
                $expectedCountry->setExpectedCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CurriculumI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(CurriculumI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setCurriculum($this);
        }

        return $this;
    }

    public function removeI18n(CurriculumI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getCurriculum() === $this) {
                $i18n->setCurriculum(null);
            }
        }

        return $this;
    }
}
