<?php

namespace App\Serializer;

use App\Entity\WorkType;
use Doctrine\Common\Collections\Collection;

final class WorkTypeSerializer
{
    public static function list(array $workTypes): array
    {
        return array_map(function ($workType) {
            return WorkTypeSerializer::details($workType);
        }, $workTypes);
    }

    public static function details(WorkType $workType): array
    {
        return [
            'id' => $workType->getId(),
            'i18n' => Serializer::i18n($workType->getI18n()),
        ];
    }
}