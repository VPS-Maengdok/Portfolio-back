<?php

namespace App\DTO;

use App\DTO\I18n\TagI18nDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class TagDTO
{
    /** @var TagI18nDTO[] */
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
    public ?array $project = [];    
}
