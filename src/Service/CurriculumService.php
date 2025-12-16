<?php

namespace App\Service;

use App\DTO\I18n\CurriculumI18nDTO;
use App\DTO\CurriculumDTO;
use App\Entity\Curriculum;
use App\Entity\CurriculumI18n;
use App\Entity\Locale;
use App\Repository\CurriculumI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CurriculumService
{
    private $relations = [
        'location',
    ];

    private $collections = [
        'education',
        'expectedCountry',
        'experience',
        'language',
        'link',
        'project',
        'skill',
        'technology',
        'workType',
        'visaAvailableFor',
    ];

    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly CurriculumI18nRepository   $curriculumI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(CurriculumDTO $dto): Curriculum
    {
        $hydratedCurriculum = $this->hydrateCurriculum(new Curriculum(), $dto);

        $this->relationService->setRelations($hydratedCurriculum, $dto, $this->relations);
        $this->relationService->setCollections($hydratedCurriculum, $dto, $this->collections);
        $this->em->persist($hydratedCurriculum);

        $this->i18nService->setCollectionOnCreate(
            $hydratedCurriculum,
            $dto->i18n,
            fn() => new CurriculumI18n(),
            fn(CurriculumI18n $i18n, CurriculumI18nDTO $i18nDTO, Locale $locale) => $this->hydrateCurriculumI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedCurriculum;
    }

    public function update(Curriculum $curriculum, CurriculumDTO $dto): Curriculum
    {
        $this->hydrateCurriculum($curriculum, $dto);
        $this->i18nService->removeCollectionOnUpdate($curriculum, $dto->i18n);
        $this->relationService->setRelations($curriculum, $dto, $this->relations);
        $this->relationService->setCollections($curriculum, $dto, $this->collections);
        $this->i18nService->setCollectionOnUpdate(
            $curriculum,
            $dto->i18n,
            fn() => new CurriculumI18n(),
            fn(CurriculumI18n $i18n, CurriculumI18nDTO $i18nDTO, Locale $locale) => $this->hydrateCurriculumI18n($i18n, $i18nDTO, $locale),
            'curriculum',
            $this->curriculumI18nRepository
        );

        $this->em->flush();

        return $curriculum;
    }

    public function delete(Curriculum $curriculum): void
    {
        $this->em->remove($curriculum);
        $this->em->flush();
    }

    private function hydrateCurriculum(Curriculum $curriculum, CurriculumDTO $dto): Curriculum
    {
        return $curriculum
            ->setFirstname($dto->firstname)
            ->setLastname($dto->lastname)
            ->setCity($dto->city)
            ->setIsFreelance($dto->isFreelance)
            ->setFreelanceCompanyName($dto->freelanceCompanyName)
            ->setIsAvailable($dto->isAvailable)
            ->setHasVisa($dto->hasVisa);
    }

    private function hydrateCurriculumI18n(CurriculumI18n $i18n, CurriculumI18nDTO $dto, Locale $locale): CurriculumI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setSlug($dto->slug)
            ->setLocale($locale);
    }
}
