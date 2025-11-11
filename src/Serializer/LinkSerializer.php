<?php

namespace App\Serializer;

use App\Entity\Language;
use App\Entity\Link;
use Doctrine\Common\Collections\Collection;

final class LinkSerializer
{
    public static function list(array $links): array
    {
        return array_map(function ($link) {
            return LinkSerializer::details($link);
        }, $links);
    }

    public static function details(Link $link): array
    {
        return [
            'id' => $link->getId(),
            'icon' => $link?->getIcon(),
            'i18n' => Serializer::i18n($link->getI18n()),
            'url' => $link?->getUrl(),
            'repositoryUrl' => $link?->getRepositoryUrl(),
            'isProject' => $link->isProject(),
            'project' => $link->getProject() ? ProjectSerializer::details($link->getProject()) : null,
            'curriculum' => $link?->getCurriculum()?->getId()
        ];
    }
}