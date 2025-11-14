<?php

namespace App\Serializer;

use App\Entity\Country;

final class CountrySerializer extends Serializer
{
    public function __construct() {}

    public function list(array $countries): array
    {
        return array_map(function ($country) {
            return $this->details($country);
        }, $countries);
    }

    public function details(Country $country, ?bool $everyLocale = false): array
    {
        return [
            'id' => $country->getId(),
            'i18n' => $everyLocale ? 
                $this->i18nComplete($country->getI18n()->toArray()) :
                $this->i18n($country->getI18n()),
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
