<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CompanyDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\Length(max: 255)]
    #[Assert\Url(protocols: ['http', 'https'], message: 'URL invalide.')]
    public ?string $url;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $city;

    #[Assert\NotNull(groups: ['create', 'update'])]
    #[Assert\Positive]
    public ?int $country;
}
