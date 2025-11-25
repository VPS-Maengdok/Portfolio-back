<?php

namespace App\DTO;

use App\DTO\I18n\ProjectI18nDTO;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final class ProjectDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Date]
    public ?DateTimeImmutable $creationDate;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Boolean]
    public ?bool $isHidden;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Positive]
    public ?int $status = null;

    #[Assert\Positive]
    public ?int $company = null;

    #[Assert\Positive]
    public ?int $school = null;

    #[Assert\Positive]
    public ?int $curriculum = null;

    /** @var ProjectI18nDTO[] */
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
    public ?array $tag = [];   

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

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $picture = [];   

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $link = [];   
}
