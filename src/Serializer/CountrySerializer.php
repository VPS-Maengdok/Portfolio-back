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

    public static function details(Country $country): array
    {
        $i18n = Serializer::first18nIteration($country->getI18n()->toArray());

        return [
            'id' => $country->getId(),
            'label' => $i18n->getLabel(),
        ];
    }

    public static function detailsWithEveryLocale(Country $country): array
    {
        $i18n = array_map(function ($locale) {
            return [
                'id' => $locale->getId(),
                'label' => $locale->getLabel(),
            ];
        }, $country->getI18n()->toArray());

        return [
            'id' => $country->getId(),
            'i18n' => $i18n,
        ];
    }

    public static function create(Country $country): array
    {
        return CountrySerializer::detailsWithEveryLocale($country);
    }

    public static function update(Country $country): array
    {
        return CountrySerializer::detailsWithEveryLocale($country);
    }
}
