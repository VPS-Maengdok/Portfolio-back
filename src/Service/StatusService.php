<?php

namespace App\Service;

use App\DTO\I18n\StatusI18nDTO;
use App\DTO\StatusDTO;
use App\Entity\Status;
use App\Entity\StatusI18n;
use App\Entity\Locale;
use App\Repository\StatusI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class StatusService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly StatusI18nRepository       $statusI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(StatusDTO $dto): Status
    {
        $hydratedStatus = new Status();

        $this->relationService->setCollections($hydratedStatus, $dto);

        $this->em->persist($hydratedStatus);

        $this->i18nService->setCollectionOnCreate(
            $hydratedStatus,
            $dto->i18n,
            fn () => new StatusI18n(),
            fn (StatusI18n $i18n, StatusI18nDTO $i18nDTO, Locale $locale) => $this->hydrateStatusI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedStatus;
    }

    public function update(Status $status, StatusDTO $dto): Status
    {
        $this->i18nService->removeCollectionOnUpdate($status, $dto->i18n);
        $this->relationService->setCollections($status, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $status,
            $dto->i18n,
            fn () => new StatusI18n(),
            fn (StatusI18n $i18n, StatusI18nDTO $i18nDTO, Locale $locale) => $this->hydrateStatusI18n($i18n, $i18nDTO, $locale),
            'status',
            $this->statusI18nRepository
        );

        $this->em->flush();

        return $status;
    }

    public function delete(Status $status): void
    {
        $this->em->remove($status);
        $this->em->flush();
    }

    private function hydrateStatusI18n(StatusI18n $i18n, StatusI18nDTO $dto, Locale $locale): StatusI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
