<?php

namespace App\Service;

use App\DTO\I18n\WorkTypeI18nDTO;
use App\DTO\WorkTypeDTO;
use App\Entity\WorkType;
use App\Entity\WorkTypeI18n;
use App\Entity\Locale;
use App\Repository\WorkTypeI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class WorkTypeService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly WorkTypeI18nRepository     $workTypeI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {
    }

    public function create(WorkTypeDTO $dto): WorkType
    {
        $hydratedWorkType = new WorkType();

        $this->relationService->setCurriculum($hydratedWorkType, $dto);

        $this->em->persist($hydratedWorkType);

        $this->i18nService->setCollectionOnCreate(
            $hydratedWorkType, 
            $dto->i18n, 
            fn () => new WorkTypeI18n(), 
            fn (WorkTypeI18n $i18n, WorkTypeI18nDTO $i18nDTO, Locale $locale) => $this->hydrateWorkTypeI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedWorkType;
    }

    public function update(WorkType $workType, WorkTypeDTO $dto): WorkType
    {
        $this->i18nService->removeCollectionOnUpdate($workType, $dto->i18n);
        $this->relationService->setCurriculum($workType, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $workType,
            $dto->i18n,
            fn () => new WorkTypeI18n(),
            fn (WorkTypeI18n $i18n, WorkTypeI18nDTO $i18nDTO, Locale $locale) => $this->hydrateWorkTypeI18n($i18n, $i18nDTO, $locale),
            'workType',
            $this->workTypeI18nRepository
        );


        $this->em->flush();

        return $workType;
    }

    public function delete(WorkType $workType): void
    {
        $this->em->remove($workType);
        $this->em->flush();
    }

    private function hydrateWorkTypeI18n(WorkTypeI18n $i18n, WorkTypeI18nDTO $dto, Locale $locale): WorkTypeI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
