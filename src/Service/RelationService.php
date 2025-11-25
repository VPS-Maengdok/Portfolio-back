<?php

namespace App\Service;

use App\Repository\CompanyRepository;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ProjectRepository;
use App\Repository\SchoolRepository;
use App\Repository\SkillRepository;
use App\Repository\StatusRepository;
use App\Repository\TagRepository;
use App\Repository\TechnologyRepository;
use LogicException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RelationService {
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository,
        private readonly CurriculumRepository $curriculumRepository,
        private readonly SchoolRepository $schoolRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly StatusRepository $statusRepository,
        private readonly TagRepository $tagRepository,
    ) {}

    public function validateArrayOfIdsOnCreate(array $ids, string $key, object $entity): void
    {
        $repository = $this->getRepository($key);
        $add = $this->getMethod('add' . ucfirst($key), $entity);

        foreach ($ids as $id) {
            if (!$result = $repository->findOneById($id)) {
                throw new NotFoundHttpException(ucfirst($key) . ' not found.');
            }

            $entity->$add($result);
        }
    }

    public function validateArrayOfIdsOnUpdate(array $ids, string $key, object $entity): void
    {
        $repository = $this->getRepository($key);
        $get = $this->getMethod('get' . ucfirst($key) . 's', $entity);
        $add = $this->getMethod('add' . ucfirst($key), $entity);
        $remove = $this->getMethod('remove' . ucfirst($key), $entity);
        $idList = [];

        if ($entity->$get()) {
            foreach ($entity->$get() as $data) {
                if (!in_array($data->getId(), $ids)) {
                    $entity->$remove($data);
                }

                $idList[] = $data->getId();
            }
        }

        foreach ($ids as $id) {
            if (!$result = $repository->findOneById($id)) {
                throw new NotFoundHttpException(ucfirst($key) . ' not found.');
            }

            if (!in_array($id, $idList)) {
                $entity->$add($result);
            }
        }
    }

    public function setCurriculum(object $entity, object $dto): void
    {
        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $entity->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $entity->setCurriculum($curriculum);
        }
    }

    public function setRelations(object $entity, object $dto): void
    {
        if ($dto->school) {
            if (!$school = $this->schoolRepository->find($dto->school)) {
                throw new NotFoundHttpException('School not found.');
            }

            $entity->setSchool($school);
        }

        if ($dto->company) {
            if (!$company = $this->companyRepository->find($dto->company)) {
                throw new NotFoundHttpException('Company not found.');
            }

            $entity->setCompany($company);
        }

        if ($dto->status) {
            if (!$status = $this->statusRepository->find($dto->status)) {
                throw new NotFoundHttpException('Status not found.');
            }

            $entity->setStatus($status);
        }
    }

    public function setCollections(object $entity, object $dto): void 
    {
        if ($dto->skill) {
            $this->validateArrayOfIdsOnCreate($dto->skill, 'skill', $entity);
        }

        if ($dto->technology) {
            $this->validateArrayOfIdsOnCreate($dto->technology, 'technology', $entity);
        }

        if ($dto->picture) {
            $this->validateArrayOfIdsOnCreate($dto->picture, 'picture', $entity);
        }

        if ($dto->link) {
            $this->validateArrayOfIdsOnCreate($dto->link, 'link', $entity);
        }

        if ($dto->tag) {
            $this->validateArrayOfIdsOnCreate($dto->tag, 'tag', $entity);
        }
    }

    public function getMethod(string $key, object $entity): string
    {
        if (!method_exists($entity, $key)) {
            throw new LogicException('Entity has no method ' . $key);
        }

        return $key;
    }

    public function getRepository(string $key): object 
    {
        return match ($key) {
            'experience' => $this->experienceRepository,
            'education'  => $this->educationRepository,
            'project'    => $this->projectRepository,
            'skill'      => $this->skillRepository,
            'technology' => $this->technologyRepository,
            'tag'        => $this->tagRepository,
            default      => throw new NotFoundHttpException("Unknown Repository key: $key"),
        };
    }
}