<?php

namespace App\Serializer;

use App\Entity\Language;

final class LanguageSerializer extends Serializer
{
    public function __construct() {}

    public function list(array $languages): array
    {
        return array_map(function ($language) {
            return $this->details($language);
        }, $languages);
    }

    public function details(Language $language, ?bool $everyLocale = false): array
    {
        return [
            'id' => $language->getId(),
            'i18n' => $everyLocale ? 
                $this->i18nComplete($language->getI18n()->toArray(), ['level']) : 
                $this->i18n($language->getI18n(), ['level']),
            'curriculum' => $language->getCurriculum()?->getId(),
        ];
    }

    public function create(Language $language): array
    {
        return $this->details($language, true);
    }

    public function update(Language $language): array
    {
        return $this->details($language, true);
    }
}