<?php

namespace App\Service;

use App\DTO\I18n\ExperienceI18nDTO;
use App\DTO\ExperienceDTO;
use App\Entity\Experience;
use App\Entity\ExperienceI18n;
use App\Entity\Locale;
use App\Repository\CompanyRepository;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceI18nRepository;
use App\Repository\ExperienceRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExperienceService
{
    public function __construct(
        private readonly RelationService $relationService,
        private readonly ExperienceI18nRepository $experienceI18nRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly CurriculumRepository $curriculumRepository,
        private readonly LocaleRepository $localeRepository,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {}

    public function create(ExperienceDTO $dto): Experience
    {
        $hydratedExperience = $this->hydrateExperience(new Experience(), $dto);

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedExperience->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedExperience->setCurriculum($curriculum);
        }

        if ($dto->company) {
            if (!$school = $this->companyRepository->find($dto->company)) {
                throw new NotFoundHttpException('School not found.');
            }

            $hydratedExperience->setCompany($school);
        }

        if ($dto->skill) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->skill, 'skill', $hydratedExperience);
        }

        if ($dto->technology) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->technology, 'technology', $hydratedExperience);
        }

        $this->em->persist($hydratedExperience);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateExperienceI18n(new ExperienceI18n(), $value, $locale);
            
            $hydratedExperience->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedExperience;
    }

    public function update(Experience $experience, ExperienceDTO $dto): Experience
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($experience->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $experience->removeI18n($existing);
            }
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $experience->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $experience->setCurriculum($curriculum);
        }

        if ($dto->company) {
            if (!$school = $this->companyRepository->find($dto->company)) {
                throw new NotFoundHttpException('School not found.');
            }

            $experience->setCompany($school);
        }

        if ($dto->skill) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->skill, 'skill', $experience);
        }

        if ($dto->technology) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->technology, 'technology', $experience);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                if ($this->experienceI18nRepository->findOneBy(['experience' => $experience, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This experience already has an i18n with this locale.');
                }

                $i18n = $this->hydrateExperienceI18n(new ExperienceI18n(), $value, $locale);

                $experience->addI18n($i18n);

            } else {
                if (!$existing = $this->experienceI18nRepository->findOneBy(['id' => $value->id, 'experience' => $experience])) {
                    throw new NotFoundHttpException('Experience i18n not found.');
                }

                $this->hydrateExperienceI18n($existing, $value, $locale);
            }
        }

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
