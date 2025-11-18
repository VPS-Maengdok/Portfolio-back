<?php

namespace App\Service;

use App\DTO\I18n\WorkTypeI18nDTO;
use App\DTO\WorkTypeDTO;
use App\Entity\WorkType;
use App\Entity\WorkTypeI18n;
use App\Entity\Locale;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\WorkTypeI18nRepository;
use App\Repository\WorkTypeRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class WorkTypeService extends Service
{
    public function __construct(
        private readonly WorkTypeRepository $workTypeRepository,
        private readonly WorkTypeI18nRepository $workTypeI18nRepository,
        private readonly CurriculumRepository $curriculumRepository,
        private readonly LocaleRepository $localeRepository,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
    ) {
        parent::__construct($projectRepository, $experienceRepository, $educationRepository);
    }

    public function create(WorkTypeDTO $dto): WorkType
    {
        $hydratedWorkType = new WorkType();

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedWorkType->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedWorkType->setCurriculum($curriculum);
        }

        $this->em->persist($hydratedWorkType);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateWorkTypeI18n(new WorkTypeI18n(), $value, $locale);
            
            $hydratedWorkType->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedWorkType;
    }

    public function update(int $id, WorkTypeDTO $dto): WorkType
    {
        if (!$workType = $this->workTypeRepository->find($id)) {
            throw new NotFoundHttpException('WorkType not found.');
        }

        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($workType->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $workType->removeI18n($existing);
            }
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $workType->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $workType->setCurriculum($curriculum);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                $i18n = $this->hydrateWorkTypeI18n(new WorkTypeI18n(), $value, $locale);
                $workType->addI18n($i18n);
            } else {
                if (!$existing = $this->workTypeI18nRepository->findOneBy(['id' => $value->id, 'workType' => $workType, 'locale' => $locale])) {
                    throw new NotFoundHttpException('WorkType i18n not found.');
                }

                $this->hydrateWorkTypeI18n($existing, $value, $locale);
            }
        }

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
