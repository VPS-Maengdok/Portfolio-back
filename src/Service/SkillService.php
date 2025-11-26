<?php

namespace App\Service;

use App\DTO\I18n\SkillI18nDTO;
use App\DTO\SkillDTO;
use App\Entity\Skill;
use App\Entity\SkillI18n;
use App\Entity\Locale;
use App\Repository\SkillI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SkillService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly SkillI18nRepository        $skillI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}
    public function create(SkillDTO $dto): Skill
    {
        $hydratedSkill = new Skill();

        $this->relationService->setCurriculum($hydratedSkill, $dto);
        $this->relationService->setCollections($hydratedSkill, $dto);

        $this->em->persist($hydratedSkill);

        $this->i18nService->setCollectionOnCreate(
            $hydratedSkill, 
            $dto->i18n, 
            fn () => new SkillI18n(), 
            fn (SkillI18n $i18n, SkillI18nDTO $i18nDTO, Locale $locale) => $this->hydrateSkillI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedSkill;
    }

    public function update(Skill $skill, SkillDTO $dto): Skill
    {
        $this->i18nService->removeCollectionOnUpdate($skill, $dto->i18n);
        $this->relationService->setCurriculum($skill, $dto);
        $this->relationService->setCollections($skill, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $skill,
            $dto->i18n,
            fn () => new SkillI18n(),
            fn (SkillI18n $i18n, SkillI18nDTO $i18nDTO, Locale $locale) => $this->hydrateSkillI18n($i18n, $i18nDTO, $locale),
            'skill',
            $this->skillI18nRepository
        );

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
