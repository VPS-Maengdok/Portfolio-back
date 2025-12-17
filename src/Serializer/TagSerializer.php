<?php

namespace App\Serializer;

use App\Entity\Tag;

final class TagSerializer extends Serializer
{
    public function list(array $tags, ?int $localeId = null): array
    {
        return array_map(function ($tag) use ($localeId) {
            return $this->details($tag, false, $localeId);
        }, $tags);
    }

    public function details(
        Tag $tag,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array
    {
        return [
            'id' => $tag->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($tag->getI18n()->toArray(), [], $localeId) :
                $this->i18n($tag->getI18n(), [], $localeId),
        ];
    }

    public function create(Tag $tag): array
    {
        return $this->details($tag, true);
    }

    public function update(Tag $tag): array
    {
        return $this->details($tag, true);
    }
}
