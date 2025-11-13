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

    public static function details(Language $language, ?bool $everyLocale = false): array
    {
        return [
            'id' => $language->getId(),
            'i18n' => $everyLocale ? 
                Serializer::i18nComplete($language->getI18n()->toArray(), ['level']) : 
                Serializer::i18n($language->getI18n(), ['level']),
            'curriculum' => $language->getCurriculum()?->getId(),
        ];
    }

    public static function create(Language $language): array
    {
        return LanguageSerializer::details($language, true);
    }

    public static function update(Language $language): array
    {
        return LanguageSerializer::details($language, true);
    }
}