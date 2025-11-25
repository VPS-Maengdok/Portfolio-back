<?php

namespace App\Service;

use App\DTO\I18n\SkillI18nDTO;
use App\DTO\SkillDTO;
use App\Entity\Skill;
use App\Entity\SkillI18n;
use App\Entity\Locale;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\SkillI18nRepository;
use App\Repository\SkillRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SkillService
{
    public function __construct(
        private readonly RelationService $relationService,
        private SkillI18nRepository $skillI18nRepository,
        private CurriculumRepository $curriculumRepository,
        private LocaleRepository $localeRepository,
        private EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {}
    public function create(SkillDTO $dto): Skill
    {
        $hydratedSkill = new Skill();

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedSkill->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedSkill->setCurriculum($curriculum);
        }

        if ($dto->experience) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->experience, 'experience', $hydratedSkill);
        }

        if ($dto->education) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->education, 'education', $hydratedSkill);
        }

        if ($dto->project) {
            $this->relationService->validateArrayOfIdsOnCreate($dto->project, 'project', $hydratedSkill);
        }

        $this->em->persist($hydratedSkill);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateSkillI18n(new SkillI18n(), $value, $locale);
            
            $hydratedSkill->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedSkill;
    }

    public function update(Skill $skill, SkillDTO $dto): Skill
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($skill->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $skill->removeI18n($existing);
            }
        }

        if ($dto->experience) {
            $this->relationService->validateArrayOfIdsOnUpdate($dto->experience, 'experience', $skill);
        }

        if ($dto->education) {
            $this->relationService->validateArrayOfIdsOnUpdate($dto->education, 'education', $skill);
        }

        if ($dto->project) {
            $this->relationService->validateArrayOfIdsOnUpdate($dto->project, 'project', $skill);
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $skill->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $skill->setCurriculum($curriculum);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                if ($this->skillI18nRepository->findOneBy(['skill' => $skill, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This skill already has an i18n with this locale.');
                }

                $i18n = $this->hydrateSkillI18n(new SkillI18n(), $value, $locale);

                $skill->addI18n($i18n);

            } else {
                if (!$existing = $this->skillI18nRepository->findOneBy(['id' => $value->id, 'skill' => $skill, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Skill i18n not found.');
                }

                $this->hydrateSkillI18n($existing, $value, $locale);
            }
        }

        $this->em->flush();

        return $skill;
    }

    public function delete(Skill $skill): void
    {
        $this->em->remove($skill);
        $this->em->flush();
    }

    private function hydrateSkillI18n(SkillI18n $i18n, SkillI18nDTO $dto, Locale $locale): SkillI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
