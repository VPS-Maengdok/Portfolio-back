<?php

namespace App\Service;

use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use LogicException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Service {
    public function __construct(
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {}

    public function validateArrayOfIdsOnCreate(array $ids, string $key, object $hydratedEntity): void
    {
        $repository = $this->getRepository($key);
        $add = $this->getMethod('add' . ucfirst($key), $hydratedEntity);

        foreach ($ids as $id) {
            if (!$result = $repository->findOneById($id)) {
                throw new NotFoundHttpException(ucfirst($key) . ' not found.');
            }

            $hydratedEntity->$add($result);
        }
    }

    public function validateArrayOfIdsOnUpdate(array $ids, string $key, object $hydratedEntity): void
    {
        $repository = $this->getRepository($key);
        $get = $this->getMethod('get' . ucfirst($key) . 's', $hydratedEntity);
        $add = $this->getMethod('add' . ucfirst($key), $hydratedEntity);
        $remove = $this->getMethod('remove' . ucfirst($key), $hydratedEntity);
        $idList = [];

        if ($hydratedEntity->$get()) {
            foreach ($hydratedEntity->$get() as $data) {
                if (!in_array($data->getId(), $ids)) {
                    $hydratedEntity->$remove($data);
                }

                $idList[] = $data->getId();
            }
        }

        foreach ($ids as $id) {
            if (!$result = $repository->findOneById($id)) {
                throw new NotFoundHttpException(ucfirst($key) . ' not found.');
            }

            if (!in_array($id, $idList)) {
                $hydratedEntity->$add($result);
            }
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
            default      => throw new NotFoundHttpException("Unknown Repository key: $key"),
        };
    }
}