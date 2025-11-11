<?php

namespace App\Serializer;

use App\Entity\Locale;

final class LocaleSerializer
{
    public static function list(array $locales): array
    {
        return array_map(function ($locale) {
            return LocaleSerializer::details($locale);
        }, $locales);
    }

    public static function details(Locale $locale): array
    {
        return [
            'id' => $locale->getId(),
            'label' => $locale->getLabel(),
        ];
    }
}