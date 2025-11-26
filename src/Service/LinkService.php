<?php

namespace App\Service;

use App\DTO\I18n\LinkI18nDTO;
use App\DTO\LinkDTO;
use App\Entity\Link;
use App\Entity\LinkI18n;
use App\Entity\Locale;
use App\Repository\LinkI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class LinkService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly LinkI18nRepository         $linkI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(LinkDTO $dto): Link
    {
        $hydratedLink = $this->hydrateLink(new Link(), $dto);

        $this->relationService->setRelations($hydratedLink, $dto);

        $this->em->persist($hydratedLink);

        $this->i18nService->setCollectionOnCreate(
            $hydratedLink, 
            $dto->i18n, 
            fn () => new LinkI18n(), 
            fn (LinkI18n $i18n, LinkI18nDTO $i18nDTO, Locale $locale) => $this->hydrateLinkI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedLink;
    }

    public function update(Link $link, LinkDTO $dto): Link
    {
        $hydratedLink = $this->hydrateLink($link, $dto);

        $this->i18nService->removeCollectionOnUpdate($hydratedLink, $dto->i18n);
        $this->relationService->setRelations($hydratedLink, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $hydratedLink,
            $dto->i18n,
            fn () => new LinkI18n(),
            fn (LinkI18n $i18n, LinkI18nDTO $i18nDTO, Locale $locale) => $this->hydrateLinkI18n($i18n, $i18nDTO, $locale),
            'link',
            $this->linkI18nRepository
        );

        $this->em->flush();

        return $hydratedLink;
    }

    public function delete(Link $link): void
    {
        $this->em->remove($link);
        $this->em->flush();
    }

    private function hydrateLink(Link $link, LinkDTO $dto): Link
    {
        return $link
            ->setIcon($dto->icon)
            ->setIsProject($dto->isProject)
            ->setUrl($dto->url)
            ->setRepositoryUrl($dto->repositoryUrl);
    }

    private function hydrateLinkI18n(LinkI18n $i18n, LinkI18nDTO $dto, Locale $locale): LinkI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
