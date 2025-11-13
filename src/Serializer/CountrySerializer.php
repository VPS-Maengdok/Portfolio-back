<?php

namespace App\Serializer;

use App\Entity\Country;

final class CountrySerializer
{
    public static function list(array $countries): array
    {
        return array_map(function ($country) {
            return CountrySerializer::details($country);
        }, $countries);
    }

    public static function details(Country $country, ?bool $everyLocale = false): array
    {
        return [
            'id' => $country->getId(),
            'i18n' => $everyLocale ? 
                Serializer::i18nComplete($country->getI18n()->toArray()) :
                Serializer::i18n($country->getI18n()),
        ];
    }

    public static function create(Country $country): array
    {
        return CountrySerializer::details($country, true);
    }

    public static function update(Country $country): array
    {
        return CountrySerializer::details($country, true);
    }
}
