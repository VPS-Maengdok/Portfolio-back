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

    public function i18n(
        Collection $i18n,
        ?array $additionalFields = [],
        ?int $localeId = null,
    ): array
    {
        if (!$translation = $this->first18nWithLocale($i18n, $localeId)) {
            return [];
        }

        $base = [
            'id' => $translation->getId(),
            'label' => $translation->getLabel(),
            'locale' => $translation->getLocale()->getId(),
        ];

        if (!$additionalFields) {
            return $base;
        }

        $additionalRows = $this->additionalMethod($additionalFields, $translation);
        return array_merge($base, $additionalRows);
    }

    public function i18nComplete(
        array $i18n,
        ?array $additionalFields = [],
        ?int $localeId = null,
    ): array
    {
        $filtered = $localeId
            ? array_filter(
                $i18n,
                fn($locale) => $locale->getLocale()->getId() === $localeId,
              )
            : $i18n;

        $result = array_values(array_map(function ($locale) use ($additionalFields) {
            $base = [
                'id' => $locale->getId(),
                'label' => $locale->getLabel(),
                'locale' => $locale->getLocale()->getId(),
            ];

            if (!$additionalFields) {
                return $base;
            }

            $additionalRows = $this->additionalMethod($additionalFields, $locale);
            return array_merge($base, $additionalRows);
        }, $filtered));

        array_multisort(array_column($result, 'id'), SORT_ASC, $result);

        return $result;
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

    private function first18nWithLocale(Collection $i18n, ?int $localeId): ?object
    {
        if (!$i18n || $localeId === null) {
            return $this->first18nIteration($i18n);
        }

        foreach ($i18n as $translation) {
            if ($translation->getLocale()->getId() === $localeId) {
                return $translation;
            }
        }

        return $this->first18nIteration($i18n);
    }
}
