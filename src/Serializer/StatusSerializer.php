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

    public function details(Status $status, ?bool $everyLocale = false): array
    {
        return [
            'id' => $status->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($status->getI18n()->toArray()) : 
                $this->i18n($status->getI18n()),
        ];
    }

    public function create(Status $status): array
    {
        return $this->details($status, true);
    }

    public function update(Status $status): array
    {
        return $this->details($status, true);
    }
}