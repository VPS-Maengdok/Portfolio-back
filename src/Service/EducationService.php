<?php

namespace App\Service;

use App\DTO\I18n\EducationI18nDTO;
use App\DTO\EducationDTO;
use App\Entity\Education;
use App\Entity\EducationI18n;
use App\Entity\Locale;
use App\Repository\EducationI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class EducationService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly EducationI18nRepository    $educationI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(EducationDTO $dto): Education
    {
        $hydratedEducation = $this->hydrateEducation(new Education(), $dto);

        $this->relationService->setCurriculum($hydratedEducation, $dto);
        $this->relationService->setRelations($hydratedEducation, $dto);
        $this->relationService->setCollections($hydratedEducation, $dto);

        $this->em->persist($hydratedEducation);

        $this->i18nService->setCollectionOnCreate(
            $hydratedEducation, 
            $dto->i18n, 
            fn () => new EducationI18n(), 
            fn (EducationI18n $i18n, EducationI18nDTO $i18nDTO, Locale $locale) => $this->hydrateEducationI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedEducation;
    }

    public function update(Education $education, EducationDTO $dto): Education
    {
        $this->i18nService->removeCollectionOnUpdate($education, $dto->i18n);
        $this->relationService->setCurriculum($education, $dto);
        $this->relationService->setRelations($education, $dto);
        $this->relationService->setCollections($education, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $education,
            $dto->i18n,
            fn () => new EducationI18n(),
            fn (EducationI18n $i18n, EducationI18nDTO $i18nDTO, Locale $locale) => $this->hydrateEducationI18n($i18n, $i18nDTO, $locale),
            'education',
            $this->educationI18nRepository
        );

        $this->em->flush();

        return $education;
    }

    public function delete(Education $education): void
    {
        $this->em->remove($education);
        $this->em->flush();
    }

    private function hydrateEducation(Education $education, EducationDTO $dto): Education
    {
        return $education
            ->setStartingDate($dto->startingDate)
            ->setEndingDate($dto->endingDate);
    }

    private function hydrateEducationI18n(EducationI18n $i18n, EducationI18nDTO $dto, Locale $locale): EducationI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setDiploma($dto->diploma)
            ->setSlug($dto->slug)
            ->setDescription($dto->description)
            ->setLocale($locale);
    }
}
