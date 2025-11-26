<?php

namespace App\Service;

use App\DTO\I18n\ExperienceI18nDTO;
use App\DTO\ExperienceDTO;
use App\Entity\Experience;
use App\Entity\ExperienceI18n;
use App\Entity\Locale;
use App\Repository\ExperienceI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ExperienceService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly ExperienceI18nRepository   $experienceI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(ExperienceDTO $dto): Experience
    {
        $hydratedExperience = $this->hydrateExperience(new Experience(), $dto);

        $this->relationService->setCurriculum($hydratedExperience, $dto);
        $this->relationService->setRelations($hydratedExperience, $dto);
        $this->relationService->setCollections($hydratedExperience, $dto);

        $this->em->persist($hydratedExperience);

        $this->i18nService->setCollectionOnCreate(
            $hydratedExperience, 
            $dto->i18n, 
            fn () => new ExperienceI18n(), 
            fn (ExperienceI18n $i18n, ExperienceI18nDTO $i18nDTO, Locale $locale) => $this->hydrateExperienceI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedExperience;
    }

    public function update(Experience $experience, ExperienceDTO $dto): Experience
    {
        $this->i18nService->removeCollectionOnUpdate($experience, $dto->i18n);
        $this->relationService->setCurriculum($experience, $dto);
        $this->relationService->setRelations($experience, $dto);
        $this->relationService->setCollections($experience, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $experience,
            $dto->i18n,
            fn () => new ExperienceI18n(),
            fn (ExperienceI18n $i18n, ExperienceI18nDTO $i18nDTO, Locale $locale) => $this->hydrateExperienceI18n($i18n, $i18nDTO, $locale),
            'experience',
            $this->experienceI18nRepository
        );

        $this->em->flush();

        return $experience;
    }

    public function delete(Experience $experience): void
    {
        $this->em->remove($experience);
        $this->em->flush();
    }

    private function hydrateExperience(Experience $experience, ExperienceDTO $dto): Experience
    {
        return $experience
            ->setStartingDate($dto->startingDate)
            ->setEndingDate($dto->endingDate)
            ->setIsCurrentWork($dto->isCurrentWork);
    }

    private function hydrateExperienceI18n(ExperienceI18n $i18n, ExperienceI18nDTO $dto, Locale $locale): ExperienceI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setSlug($dto->slug)
            ->setDescription($dto->description)
            ->setLocale($locale);
    }
}
