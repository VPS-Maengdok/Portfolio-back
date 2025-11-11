<?php

namespace App\Serializer;

use App\Entity\Status;
use Doctrine\Common\Collections\Collection;

final class StatusSerializer
{
    public static function list(array $statuses): array
    {
        return array_map(function ($status) {
            return StatusSerializer::details($status);
        }, $statuses);
    }

    public static function details(Status $status): array
    {
        return [
            'id' => $status->getId(),
            'i18n' => Serializer::i18n($status->getI18n()),
        ];
    }
}