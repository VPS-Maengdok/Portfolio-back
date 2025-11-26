<?php

namespace App\Service;

use App\DTO\I18n\TagI18nDTO;
use App\DTO\TagDTO;
use App\Entity\Tag;
use App\Entity\TagI18n;
use App\Entity\Locale;
use App\Repository\TagI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class TagService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly TagI18nRepository          $tagI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(TagDTO $dto): Tag
    {
        $hydratedTag = new Tag();

        $this->relationService->setCollections($hydratedTag, $dto);
        $this->em->persist($hydratedTag);

        $this->i18nService->setCollectionOnCreate(
            $hydratedTag,
            $dto->i18n,
            fn () => new TagI18n(),
            fn (TagI18n $i18n, TagI18nDTO $i18nDTO, Locale $locale) => $this->hydrateTagI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedTag;
    }

    public function update(Tag $tag, TagDTO $dto): Tag
    {
        $this->i18nService->removeCollectionOnUpdate($tag, $dto->i18n);
        $this->relationService->setCollections($tag, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $tag,
            $dto->i18n,
            fn () => new TagI18n(),
            fn (TagI18n $i18n, TagI18nDTO $i18nDTO, Locale $locale) => $this->hydrateTagI18n($i18n, $i18nDTO, $locale),
            'tag',
            $this->tagI18nRepository
        );
        
        $this->em->flush();

        return $tag;
    }

    public function delete(Tag $tag): void
    {
        $this->em->remove($tag);
        $this->em->flush();
    }

    private function hydrateTagI18n(TagI18n $i18n, TagI18nDTO $dto, Locale $locale): TagI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
