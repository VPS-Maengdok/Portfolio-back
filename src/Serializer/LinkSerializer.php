<?php

namespace App\Serializer;

use App\Entity\Link;

final class LinkSerializer
{
    public static function list(array $links): array
    {
        return array_map(function ($link) {
            return LinkSerializer::details($link);
        }, $links);
    }

    public static function details(Link $link, ?bool $everyLocale = false): array
    {
        return [
            'id' => $link->getId(),
            'icon' => $link?->getIcon(),
            'i18n' => $everyLocale ? Serializer::i18nComplete($link->getI18n()->toArray()) : Serializer::i18n($link->getI18n()),
            'url' => $link?->getUrl(),
            'repositoryUrl' => $link?->getRepositoryUrl(),
            'isProject' => $link->isProject(),
            'project' => $link->getProject()?->getId(),
            'curriculum' => $link->getCurriculum()?->getId()
        ];
    }

    public static function create(Link $link): array
    {
        return LinkSerializer::details($link, true);
    }

    public static function update(Link $link): array
    {
        return LinkSerializer::details($link, true);
    }
}