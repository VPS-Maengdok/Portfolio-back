<?php

namespace App\Serializer;

use App\Entity\Technology;

final class TechnologySerializer
{
    public static function list(array $technologies): array
    {
        $result = array_map(function ($technology) {
            return TechnologySerializer::details($technology);
        }, $technologies);

        array_multisort(array_column($result, 'id'), SORT_ASC, $result);

        return $result;
    }

    public static function details(Technology $technology): array
    {
        return [
            'id' => $technology->getId(),
            'icon' => $technology?->getIcon(),
            'label' => $technology->getLabel(),
            'curriculum' => $technology->getCurriculum()?->getId(),
        ];
    }
    public function create(Technology $technology): array
    {
        return $this->details($technology);
    }

    public function update(Technology $technology): array
    {
        return $this->details($technology);
    }
}