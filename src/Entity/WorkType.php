<?php

namespace App\Entity;

use App\Repository\WorkTypeRepository;
use App\Entity\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTypeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class WorkType
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, WorkTypeI18n>
     */
    #[ORM\OneToMany(targetEntity: WorkTypeI18n::class, mappedBy: 'workType', cascade: ['persist'], orphanRemoval: true)]
    private Collection $i18n;

    #[ORM\ManyToOne(inversedBy: 'workType')]
    private ?Curriculum $curriculum = null;

    public function __construct()
    {
        $this->i18n = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, WorkTypeI18n>
     */
    public function getI18n(): Collection
    {
        return $this->i18n;
    }

    public function addI18n(WorkTypeI18n $i18n): static
    {
        if (!$this->i18n->contains($i18n)) {
            $this->i18n->add($i18n);
            $i18n->setWorkType($this);
        }

        return $this;
    }

    public function removeI18n(WorkTypeI18n $i18n): static
    {
        if ($this->i18n->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getWorkType() === $this) {
                $i18n->setWorkType(null);
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
