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

    public function details(Tag $tag): array
    {
        return [
            'id' => $tag->getId(),
            'i18n' => $this->i18n($tag->getI18n()),
        ];
    }
}