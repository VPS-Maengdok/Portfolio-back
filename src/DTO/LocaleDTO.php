<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class LocaleDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $shortened;
}
