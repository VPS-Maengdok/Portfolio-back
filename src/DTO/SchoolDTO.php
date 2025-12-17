<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class SchoolDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $url;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $city;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Positive]
    public ?int $country;
}
