<?php

namespace App\Serializer;

use App\Entity\WorkType;

final class WorkTypeSerializer extends Serializer
{
    public function list(array $workTypes, ?int $localeId = null): array
    {
        return array_map(function ($workType) use ($localeId) {
            return $this->details($workType, false, $localeId);
        }, $workTypes);
    }

    public function details(
        WorkType $workType,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array
    {
        return [
            'id' => $workType->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($workType->getI18n()->toArray(), [], $localeId) :
                $this->i18n($workType->getI18n(), [], $localeId),
            'curriculum' => $workType->getCurriculum()?->getId(),
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
