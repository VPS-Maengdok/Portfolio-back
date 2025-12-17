<?php

namespace App\Service;

use App\DTO\I18n\ProjectI18nDTO;
use App\DTO\ProjectDTO;
use App\Entity\Project;
use App\Entity\ProjectI18n;
use App\Entity\Locale;
use App\Repository\ProjectI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly I18nService                $i18nService,
        private readonly ProjectI18nRepository      $projectI18nRepository,
        private readonly EntityManagerInterface     $em,
    ) {}

    public function create(ProjectDTO $dto): Project
    {
        $hydratedProject = $this->hydrateProject(new Project(), $dto);

        $this->relationService->setCurriculum($hydratedProject, $dto);
        $this->relationService->setRelations($hydratedProject, $dto);
        $this->relationService->setCollections($hydratedProject, $dto);

        $this->em->persist($hydratedProject);

        $this->i18nService->setCollectionOnCreate(
            $hydratedProject,
            $dto->i18n,
            fn() => new ProjectI18n(),
            fn(ProjectI18n $i18n, ProjectI18nDTO $i18nDTO, Locale $locale) => $this->hydrateProjectI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedProject;
    }

    public function update(Project $project, ProjectDTO $dto): Project
    {
        $this->hydrateProject($project, $dto);
        $this->i18nService->removeCollectionOnUpdate($project, $dto->i18n);
        $this->relationService->setCurriculum($project, $dto);
        $this->relationService->setRelations($project, $dto);
        $this->relationService->setCollections($project, $dto);
        $this->i18nService->setCollectionOnUpdate(
            $project,
            $dto->i18n,
            fn() => new ProjectI18n(),
            fn(ProjectI18n $i18n, ProjectI18nDTO $i18nDTO, Locale $locale) => $this->hydrateProjectI18n($i18n, $i18nDTO, $locale),
            'project',
            $this->projectI18nRepository
        );

        $this->em->flush();

        return $project;
    }

    public function delete(Project $project): void
    {
        $this->em->remove($project);
        $this->em->flush();
    }

    private function hydrateProject(Project $project, ProjectDTO $dto): Project
    {
        return $project
            ->setCreationDate($dto->creationDate)
            ->setIsHidden($dto->isHidden);
    }

    private function hydrateProjectI18n(ProjectI18n $i18n, ProjectI18nDTO $dto, Locale $locale): ProjectI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setSlug($dto->slug)
            ->setDescription($dto->description)
            ->setShortDescription($dto->shortDescription)
            ->setCvDescription($dto->cvDescription)
            ->setLocale($locale);
    }
}
