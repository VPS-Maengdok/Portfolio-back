<?php

namespace App\DTO;

use App\DTO\I18n\LinkI18nDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class LinkDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $icon;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Boolean]
    public ?bool $isProject;

    #[Assert\Length(max: 255)]
    public ?string $url;

    #[Assert\Length(max: 255)]
    public ?string $repositoryUrl;

    /** @var LinkI18nDTO[] */
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Valid]
    public ?array $i18n = [];

    #[Assert\Positive]
    public ?int $project;

    #[Assert\Positive]
    public ?int $curriculum;
}
