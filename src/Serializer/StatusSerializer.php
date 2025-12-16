<?php

namespace App\Serializer;

use App\Entity\Status;

final class StatusSerializer extends Serializer
{
    public function __construct() {}

    public function list(array $statuses, ?int $localeId = null): array
    {
        return array_map(function ($status) use ($localeId) {
            return $this->details($status, false, $localeId);
        }, $statuses);
    }

    public function details(
        Status $status,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array
    {
        return [
            'id' => $status->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($status->getI18n()->toArray(), [], $localeId) :
                $this->i18n($status->getI18n(), [], $localeId),
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
