<?php

namespace App\Serializer;

use App\Entity\Status;

final class StatusSerializer extends Serializer
{
    public function __construct() {}

    public function list(array $statuses): array
    {
        return array_map(function ($status) {
            return $this->details($status);
        }, $statuses);
    }

    public function details(Status $status): array
    {
        return [
            'id' => $status->getId(),
            'i18n' => $this->i18n($status->getI18n()),
        ];
    }
}