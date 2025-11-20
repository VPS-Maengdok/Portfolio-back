<?php

namespace App\Service;

use App\DTO\TechnologyDTO;
use App\Entity\Technology;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\TechnologyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TechnologyService extends Service
{
    public function __construct(
        private readonly CurriculumRepository $curriculumRepository,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {
        parent::__construct($projectRepository, $experienceRepository, $educationRepository, $skillRepository, $technologyRepository);
    }

    public function create(TechnologyDTO $dto): Technology
    {
        $hydratedTechnology = $this->hydrateTechnology(new Technology(), $dto);

        if ($dto->experience) {
            $this->validateArrayOfIdsOnCreate($dto->experience, 'experience', $hydratedTechnology);
        }

        if ($dto->education) {
            $this->validateArrayOfIdsOnCreate($dto->education, 'education', $hydratedTechnology);
        }
        
        if ($dto->project) {
            $this->validateArrayOfIdsOnCreate($dto->project, 'project', $hydratedTechnology);
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedTechnology->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedTechnology->setCurriculum($curriculum);
        }

        $this->em->persist($hydratedTechnology);
        $this->em->flush();

        return $hydratedTechnology;
    }

    public function update(Technology $technology, TechnologyDTO $dto): Technology
    {
        if ($dto->experience) {
            $this->validateArrayOfIdsOnCreate($dto->experience, 'experience', $technology);
        }

        if ($dto->education) {
            $this->validateArrayOfIdsOnCreate($dto->education, 'education', $technology);
        }
        
        if ($dto->project) {
            $this->validateArrayOfIdsOnCreate($dto->project, 'project', $technology);
        }

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $technology->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $technology->setCurriculum($curriculum);
        }

        $this->hydrateTechnology($technology, $dto);
        $this->em->flush();

        return $technology;
    }

    public function delete(Technology $technology): void
    {
        $this->em->remove($technology);
        $this->em->flush();
    }

    private function hydrateTechnology(Technology $technology, TechnologyDTO $dto): Technology
    {
        return $technology
            ->setLabel($dto->label)
            ->setIcon($dto->icon);
    }
}
