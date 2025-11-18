<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class TechnologyDTO
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $label;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 255)]
    public ?string $icon;

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $experience = []; 

        /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $education = []; 

    /**
     * @var int[]
     */
    #[Assert\Type('array')]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive(),
    ])]
    public ?array $project = [];    

    #[Assert\Positive]
    public ?int $curriculum = null;
}
