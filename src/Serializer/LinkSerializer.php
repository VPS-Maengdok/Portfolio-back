<?php

namespace App\Serializer;

use App\Entity\Link;

final class LinkSerializer extends Serializer
{
    public function __construct() {}

    public function list(array $links): array
    {
        return array_map(function ($link) {
            return $this->details($link);
        }, $links);
    }

    public function details(Link $link, ?bool $everyLocale = false): array
    {
        return [
            'id' => $link->getId(),
            'icon' => $link?->getIcon(),
            'i18n' => $everyLocale ? 
                $this->i18nComplete($link->getI18n()->toArray()) : 
                $this->i18n($link->getI18n()),
            'url' => $link?->getUrl(),
            'repositoryUrl' => $link?->getRepositoryUrl(),
            'isProject' => $link->isProject(),
            'project' => $link->getProject()?->getId(),
            'curriculum' => $link->getCurriculum()?->getId()
        ];
    }

    public function create(Link $link): array
    {
        return $this->details($link, true);
    }

    public function update(Link $link): array
    {
        return $this->details($link, true);
    }
}