<?php

namespace App\Service;

use App\DTO\I18n\StatusI18nDTO;
use App\DTO\StatusDTO;
use App\Entity\Status;
use App\Entity\StatusI18n;
use App\Entity\Locale;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\StatusI18nRepository;
use App\Repository\StatusRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StatusService extends Service
{
    public function __construct(
        private readonly StatusRepository $statusRepository,
        private readonly StatusI18nRepository $statusI18nRepository,
        private readonly LocaleRepository $localeRepository,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {
        parent::__construct($projectRepository, $experienceRepository, $educationRepository, $skillRepository, $technologyRepository);
    }

    public function create(StatusDTO $dto): Status
    {
        $hydratedStatus = new Status();

        if ($dto->project) {
            $this->validateArrayOfIdsOnCreate($dto->project, 'project', $hydratedStatus);
        }

        $this->em->persist($hydratedStatus);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateStatusI18n(new StatusI18n(), $value, $locale);
            
            $hydratedStatus->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedStatus;
    }

    public function update(Status $status, StatusDTO $dto): Status
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($status->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $status->removeI18n($existing);
            }
        }

        if ($dto->project) {
            $this->validateArrayOfIdsOnUpdate($dto->project, 'project', $status);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                $i18n = $this->hydrateStatusI18n(new StatusI18n(), $value, $locale);
                $status->addI18n($i18n);
            } else {
                if (!$existing = $this->statusI18nRepository->findOneBy(['id' => $value->id, 'status' => $status, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Status i18n not found.');
                }

                $this->hydrateStatusI18n($existing, $value, $locale);
            }
        }

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
