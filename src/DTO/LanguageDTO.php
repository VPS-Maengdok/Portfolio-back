<?php

namespace App\DTO;

use App\DTO\I18n\LanguageI18nDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class LanguageDTO
{
    /** @var LanguageI18nDTO[] */
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Valid]
    public ?array $i18n = [];

    #[Assert\Positive]
    public ?int $curriculum = null;
}
