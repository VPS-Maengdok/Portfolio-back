<?php

namespace App\Serializer;

use App\Entity\Technology;

final class TechnologySerializer
{
    public static function list(array $technologies): array
    {
        return array_map(function ($technology) {
            return TechnologySerializer::details($technology);
        }, $technologies);
    }

    public static function details(Technology $technology): array
    {
        return [
            'id' => $technology->getId(),
            'icon' => $technology?->getIcon(),
            'label' => $technology->getLabel(),
        ];
    }
}