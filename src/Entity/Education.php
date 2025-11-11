<?php

namespace App\Entity;

use App\Repository\EducationRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EducationRepository::class)]
class Education
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startingDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endingDate = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?School $school = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'education')]
    private Collection $skill;

    /**
     * @var Collection<int, Technology>
     */
    #[ORM\ManyToMany(targetEntity: Technology::class, inversedBy: 'education')]
    private Collection $technology;

    /**
     * @var Collection<int, EducationI18n>
     */
    #[ORM\OneToMany(targetEntity: EducationI18n::class, mappedBy: 'education', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    #[ORM\ManyToOne(inversedBy: 'education')]
    private ?Curriculum $curriculum = null;

    public function __construct()
    {
        $this->skill = new ArrayCollection();
        $this->technology = new ArrayCollection();
        $this->i18n = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartingDate(): ?\DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function setStartingDate(\DateTimeImmutable $startingDate): static
    {
        $this->startingDate = $startingDate;

        return $this;
    }

    public function getEndingDate(): ?\DateTimeImmutable
    {
        return $this->endingDate;
    }

    public function setEndingDate(?\DateTimeImmutable $endingDate): static
    {
        $this->endingDate = $endingDate;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

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
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skill->removeElement($skill);

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
        }

        return $this;
    }

    public function removeTechnology(Technology $technology): static
    {
        $this->technology->removeElement($technology);

        return $this;
    }

    /**
     * @return Collection<int, EducationI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(EducationI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setEducation($this);
        }

        return $this;
    }

    public function removeI18n(EducationI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getEducation() === $this) {
                $i18n->setEducation(null);
            }
        }

        return $this;
    }

    public function getCurriculum(): ?Curriculum
    {
        return $this->curriculum;
    }

    public function setCurriculum(?Curriculum $curriculum): static
    {
        $this->curriculum = $curriculum;

        return $this;
    }
}
