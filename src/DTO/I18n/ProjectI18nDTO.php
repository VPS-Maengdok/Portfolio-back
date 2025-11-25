<?php

namespace App\DTO\I18n;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

final class ProjectI18nDTO
{
    #[Assert\Positive(groups: ['update'])]
    public ?int $id = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $slug;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Type(type: Types::TEXT)]
    public ?string $description;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Type(type: Types::TEXT)]
    public ?string $shortDescription;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Type(type: Types::TEXT)]
    public ?string $cvDescription;

    #[Assert\NotBlank]
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $locale = null;
}
