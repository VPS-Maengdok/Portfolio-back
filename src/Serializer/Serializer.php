<?php

namespace App\Serializer;

use Doctrine\Common\Collections\Collection;

class Serializer
{
    public function __construct() {}

    public function first18nIteration(?iterable $i18n): ?object
    {
        if (!$i18n) {
            return null;
        }

        foreach ($i18n as $translation) {
            return $translation;
        }

        return null;
    }

    public function i18n(Collection $i18n, ?array $additionalFields = []): array
    {        
        if (!$translation = $this->first18nIteration($i18n)) {
            return [];
        }

        $base = [
            'id' => $translation->getId(),
            'label' => $translation->getLabel(),   
        ];

        if (!$additionalFields) {
            return $base;
        }

        $additionalRows = $this->additionalMethod($additionalFields, $translation);
        return array_merge($base, $additionalRows);
    }

    public function i18nComplete(array $i18n, ?array $additionalFields = []): array
    {
        return array_map(function ($locale) use ($additionalFields) {
            $base = [
                'id' => $locale->getId(),
                'label' => $locale->getLabel(),
            ];

            if (!$additionalFields) {
                return $base;
            }
    
            $additionalRows = $this->additionalMethod($additionalFields, $locale);
            return array_merge($base, $additionalRows);
        }, $i18n);
    }

    private function additionalMethod(array $additionalFields, object $translation): array
    {
        $result = [];

        foreach ($additionalFields as $prop) {
            $getter = 'get' . ucfirst($prop);
            $isGetter = 'is' . ucfirst($prop);

            if (method_exists($translation, $getter)) {
                $result[$prop] = $translation->$getter();
            } else if (method_exists($translation, $isGetter)) {
                $result[$prop] = $translation->$isGetter();
            } else {
                $result[$prop] = null;
            }
        }

        return $result;
    }
}