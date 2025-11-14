<?php

namespace App\Serializer;

use App\Entity\WorkType;

final class WorkTypeSerializer extends Serializer
{
    public function list(array $workTypes): array
    {
        return array_map(function ($workType) {
            return $this->details($workType);
        }, $workTypes);
    }

    public function details(WorkType $workType): array
    {
        return [
            'id' => $workType->getId(),
            'i18n' => $this->i18n($workType->getI18n()),
        ];
    }
}