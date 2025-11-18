<?php

namespace App\Serializer;

use App\Entity\Tag;

final class TagSerializer extends Serializer
{
    public function list(array $tags): array
    {
        return array_map(function ($tag) {
            return $this->details($tag);
        }, $tags);
    }

    public function details(Tag $tag, ?bool $everyLocale = false): array
    {
        return [
            'id' => $tag->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($tag->getI18n()->toArray()) :
                $this->i18n($tag->getI18n()),
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