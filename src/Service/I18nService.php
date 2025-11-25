<?php

namespace App\Service;

use App\Entity\Locale;
use App\Repository\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class I18nService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LocaleRepository $localeRepository
    ) {}

    public function setCollectionOnCreate(object $entity, iterable $i18nDTO, object $i18nEntity, callable $hydrator): void
    {
        foreach ($i18nDTO as $dto) {
            $locale = $this->getLocale($dto->locale);
            $i18n = $hydrator($i18nEntity, $dto, $locale);
            $entity->addI18n($i18n);
            $this->em->persist($i18n);
        }
    }

    public function setCollectionOnUpdate(object $entity, iterable $i18nDTO, object $i18nEntity, callable $hydrator, string $entityName, object $i18nRepository): void
    {
        foreach ($i18nDTO as $dto) {
            $locale = $this->getLocale($dto->locale);

            if (!isset($dto->id)) {
                $this->assertNoDuplicate($entity, $i18nRepository, $locale, $entityName);
                $i18n = $hydrator($i18nEntity, $dto, $locale);
                $entity->addI18n($i18n);
            } else {
                $existing = $this->getExistingI18n($entity, $dto->id, $i18nRepository, $entityName);
                $i18n = $hydrator($existing, $dto, $locale);
            }

            $this->em->persist($i18n);
        }
    }

    public function removeCollectionOnUpdate(object $entity, iterable $i18nDTO): void
    {
        $payloadIds = [];
        foreach ($i18nDTO as $dto) {
            if (isset($dto->id)) {
                $payloadIds[] = $dto->id;
            }
        }

        foreach ($entity->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $entity->removeI18n($existing);
            }
        }
    }

    private function getLocale(?int $id): Locale
    {
        if ($id === null) {
            throw new BadRequestHttpException('Locale is required.');
        }

        if (!$locale = $this->localeRepository->find($id)) {
            throw new NotFoundHttpException('Invalid Locale id.');
        }

        return $locale;
    }

    private function assertNoDuplicate(object $entity, object $i18nRepository, Locale $locale, string $entityName): void
    {
        if ($i18nRepository->findOneBy([$entityName => $entity, 'locale' => $locale])) {
            throw new BadRequestHttpException("This $entityName already has an i18n with this locale.");
        }

    }

    private function getExistingI18n(object $entity, int $id, object $i18nRepository, string $entityName): object
    {
        if (!$existing = $i18nRepository->findOneBy(['id' => $id, $entityName => $entity])) {
            throw new NotFoundHttpException(ucfirst($entityName) . ' i18n not found.');
        }

        return $existing;
    }
}