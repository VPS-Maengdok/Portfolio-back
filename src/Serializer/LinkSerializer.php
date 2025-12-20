<?php

namespace App\Serializer;

use App\Entity\Link;

final class LinkSerializer extends Serializer
{
    public function __construct() {}

    public function list(array $links, ?int $localeId = null): array
    {
        return array_map(function ($link) use ($localeId) {
            return $this->details($link, false, $localeId);
        }, $links);
    }

    public function details(
        Link $link,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array {
        return [
            'id' => $link->getId(),
            'icon' => $link?->getIcon(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($link->getI18n()->toArray()) :
                $this->i18n($link->getI18n(), [], $localeId),
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
