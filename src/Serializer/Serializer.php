<?php

namespace App\Serializer;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Exception;

class Serializer
{
    public static function first18nIteration(?iterable $i18n): ?object
    {
        if (!$i18n) {
            return null;
        }

        foreach ($i18n as $translation) {
            return $translation;
        }

        return null;
    }

    public static function i18n(Collection $i18n, ?array $additionalFields = []): array
    {        
        if (!$translation = Serializer::first18nIteration($i18n)) {
            return [];
        }

        $base = [
            'id' => $translation->getId(),
            'label' => $translation->getLabel(),   
        ];

        if (!$additionalFields) {
            return $base;
        }

        $additionalRows = Serializer::additionalMethod($additionalFields, $translation);
        return array_merge($base, $additionalRows);
    }

    private static function additionalMethod(array $additionalFields, object $translation): array
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