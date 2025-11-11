<?php

namespace App\Serializer;

use App\Entity\Tag;
use Doctrine\Common\Collections\Collection;

final class TagSerializer
{
    public static function list(array $tags): array
    {
        return array_map(function ($tag) {
            return TagSerializer::details($tag);
        }, $tags);
    }

    public static function details(Tag $tag): array
    {
        return [
            'id' => $tag->getId(),
            'i18n' => Serializer::i18n($tag->getI18n()),
        ];
    }
}