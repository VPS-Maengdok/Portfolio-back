<?php

namespace App\Serializer;

use App\Entity\Language;
use Doctrine\Common\Collections\Collection;

final class LanguageSerializer
{
    public static function list(array $languages): array
    {
        return array_map(function ($language) {
            return LanguageSerializer::details($language);
        }, $languages);
    }

    public static function details(Language $language): array
    {
        return [
            'id' => $language->getId(),
            'i18n' => Serializer::i18n($language->getI18n(), ['level']),
        ];
    }
}