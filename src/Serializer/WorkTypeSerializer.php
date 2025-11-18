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

    public function details(WorkType $workType, ?bool $everyLocale = false): array
    {
        return [
            'id' => $workType->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($workType->getI18n()->toArray()) :
                $this->i18n($workType->getI18n()),
        ];
    }

    public function create(WorkType $workType): array
    {
        return $this->details($workType, true);
    }

    public function update(WorkType $workType): array
    {
        return $this->details($workType, true);
    }
}