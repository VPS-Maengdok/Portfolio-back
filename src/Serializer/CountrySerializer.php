<?php

namespace App\Serializer;

use App\Entity\Country;

final class CountrySerializer extends Serializer
{
    public function __construct() {}

    public function list(array $countries, ?int $localeId = null): array
    {
        return array_map(function ($country) use ($localeId) {
            return $this->details($country, false, $localeId);
        }, $countries);
    }

    public function details(
        Country $country,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array {
        return [
            'id' => $country->getId(),
            'shortened' => $country?->getShortened(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($country->getI18n()->toArray()) :
                $this->i18n($country->getI18n(), [], $localeId),
        ];
    }

    public function create(Country $country): array
    {
        return $this->details($country, true);
    }

    public function update(Country $country): array
    {
        return $this->details($country, true);
    }
}
