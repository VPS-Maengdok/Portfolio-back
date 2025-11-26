<?php

namespace App\Service;

use App\DTO\I18n\LanguageI18nDTO;
use App\DTO\LanguageDTO;
use App\Entity\Language;
use App\Entity\LanguageI18n;
use App\Entity\Locale;
use App\Repository\LanguageI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class LanguageService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly LanguageI18nRepository     $languageI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(LanguageDTO $dto): Language
    {
        $hydratedLanguage = new Language();

        $this->relationService->setCurriculum($hydratedLanguage, $dto);

        $this->em->persist($hydratedLanguage);
        $this->i18nService->setCollectionOnCreate(
            $hydratedLanguage, 
            $dto->i18n, 
            fn () => new LanguageI18n(), 
            fn (LanguageI18n $i18n, LanguageI18nDTO $i18nDTO, Locale $locale) => $this->hydrateLanguageI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedLanguage;
    }

    public function update(Language $language, LanguageDTO $dto): Language
    {
        $this->i18nService->removeCollectionOnUpdate($language, $dto->i18n);
        $this->relationService->setCurriculum($language, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $language,
            $dto->i18n,
            fn () => new LanguageI18n(),
            fn (LanguageI18n $i18n, LanguageI18nDTO $i18nDTO, Locale $locale) => $this->hydrateLanguageI18n($i18n, $i18nDTO, $locale),
            'language',
            $this->languageI18nRepository
        );

        $this->em->flush();

        return $language;
    }

    public function delete(Language $language): void
    {
        $this->em->remove($language);
        $this->em->flush();
    }

    private function hydrateLanguageI18n(LanguageI18n $i18n, LanguageI18nDTO $dto, Locale $locale): LanguageI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLevel($dto->level)
            ->setLocale($locale);
    }
}
