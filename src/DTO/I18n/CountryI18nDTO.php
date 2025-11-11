<?php

namespace App\DTO\I18n;

use Symfony\Component\Validator\Constraints as Assert;

final class CountryI18nDTO
{
    #[Assert\Positive(groups: ['update'])]
    public ?int $id = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank]
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $locale = null;
}
