<?php

namespace App\Service;

use App\DTO\I18n\EducationI18nDTO;
use App\DTO\EducationDTO;
use App\Entity\Education;
use App\Entity\EducationI18n;
use App\Entity\Locale;
use App\Repository\CurriculumRepository;
use App\Repository\EducationI18nRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use App\Repository\SchoolRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EducationService extends Service
{
    public function __construct(
        private readonly EducationI18nRepository $educationI18nRepository,
        private readonly SchoolRepository $schoolRepository,
        private readonly CurriculumRepository $curriculumRepository,
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

    public function create(EducationDTO $dto): Education
    {
        $hydratedEducation = $this->hydrateEducation(new Education(), $dto);

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedEducation->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedEducation->setCurriculum($curriculum);
        }

        if ($dto->school) {
            if (!$school = $this->schoolRepository->find($dto->school)) {
                throw new NotFoundHttpException('School not found.');
            }

            $hydratedEducation->setSchool($school);
        }

        if ($dto->skill) {
            $this->validateArrayOfIdsOnCreate($dto->skill, 'skill', $hydratedEducation);
        }

        if ($dto->technology) {
            $this->validateArrayOfIdsOnCreate($dto->technology, 'technology', $hydratedEducation);
        }

        $this->em->persist($hydratedEducation);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateEducationI18n(new EducationI18n(), $value, $locale);
            
            $hydratedEducation->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedEducation;
    }

    public function update(Education $education, EducationDTO $dto): Education
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($education->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $education->removeI18n($existing);
            }
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $education->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $education->setCurriculum($curriculum);
        }

        if ($dto->school) {
            if (!$school = $this->schoolRepository->find($dto->school)) {
                throw new NotFoundHttpException('School not found.');
            }

            $education->setSchool($school);
        }

        if ($dto->skill) {
            $this->validateArrayOfIdsOnCreate($dto->skill, 'skill', $education);
        }

        if ($dto->technology) {
            $this->validateArrayOfIdsOnCreate($dto->technology, 'technology', $education);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                if ($this->educationI18nRepository->findOneBy(['education' => $education, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This education already has an i18n with this locale.');
                }

                $i18n = $this->hydrateEducationI18n(new EducationI18n(), $value, $locale);

                $education->addI18n($i18n);

            } else {
                if (!$existing = $this->educationI18nRepository->findOneBy(['id' => $value->id, 'education' => $education])) {
                    throw new NotFoundHttpException('Education i18n not found.');
                }

                $this->hydrateEducationI18n($existing, $value, $locale);
            }
        }

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
