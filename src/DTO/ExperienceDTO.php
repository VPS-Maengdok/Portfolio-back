<?php

namespace App\DTO;

use App\DTO\I18n\ExperienceI18nDTO;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class ExperienceDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Date]
    public ?DateTimeImmutable $startingDate;

    #[Assert\Date]
    public ?DateTimeImmutable $endingDate = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Boolean]
    public ?bool $isCurrentWork;

    #[Assert\Positive]
    public ?int $company = null;

    #[Assert\Positive]
    public ?int $curriculum = null;

    /** @var ExperienceI18nDTO[] */
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Valid]
    public ?array $i18n = [];

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $skill = [];   

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $technology = [];   
}
