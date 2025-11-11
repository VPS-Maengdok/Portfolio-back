<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\HasLifecycleCallbacks]
trait TimestampableTrait
{
    #[ORM\Column(TYPES::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->createdAt = $now;
        $this->updatedAt = \DateTime::createFromImmutable($now);
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
