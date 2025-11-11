<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillRepository::class)]
class Skill
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SkillI18n>
     */
    #[ORM\OneToMany(targetEntity: SkillI18n::class, mappedBy: 'skill', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    /**
     * @var Collection<int, Experience>
     */
    #[ORM\ManyToMany(targetEntity: Experience::class, mappedBy: 'skill')]
    private Collection $experiences;

    /**
     * @var Collection<int, Education>
     */
    #[ORM\ManyToMany(targetEntity: Education::class, mappedBy: 'skill')]
    private Collection $education;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'skill')]
    private Collection $projects;

    #[ORM\ManyToOne(inversedBy: 'skill')]
    private ?Curriculum $curriculum = null;

    public function __construct()
    {
        $this->i18n = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SkillI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(SkillI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setSkill($this);
        }

        return $this;
    }

    public function removeI18n(SkillI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getSkill() === $this) {
                $i18n->setSkill(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): static
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->addSkill($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): static
    {
        if ($this->experiences->removeElement($experience)) {
            $experience->removeSkill($this);
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
            $education->addSkill($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): static
    {
        if ($this->education->removeElement($education)) {
            $education->removeSkill($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addSkill($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeSkill($this);
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
