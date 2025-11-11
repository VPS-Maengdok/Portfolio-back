<?php

namespace App\DTO;

use App\DTO\I18n\CountryI18nDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class CountryDTO
{
    /** @var CountryI18nDTO[] */
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Valid]
    public array $i18n = [];
}
