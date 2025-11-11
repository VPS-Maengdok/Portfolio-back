<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
class Link
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column]
    private ?bool $isProject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repositoryUrl = null;

    /**
     * @var Collection<int, LinkI18n>
     */
    #[ORM\OneToMany(targetEntity: LinkI18n::class, mappedBy: 'link', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    #[ORM\ManyToOne(inversedBy: 'link')]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'link')]
    private ?Curriculum $curriculum = null;

    public function __construct()
    {
        $this->i18n = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isProject(): ?bool
    {
        return $this->isProject;
    }

    public function setIsProject(bool $isProject): static
    {
        $this->isProject = $isProject;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getRepositoryUrl(): ?string
    {
        return $this->repositoryUrl;
    }

    public function setRepositoryUrl(?string $repositoryUrl): static
    {
        $this->repositoryUrl = $repositoryUrl;

        return $this;
    }

    /**
     * @return Collection<int, LinkI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(LinkI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setLink($this);
        }

        return $this;
    }

    public function removeI18n(LinkI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getLink() === $this) {
                $i18n->setLink(null);
            }
        }

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
