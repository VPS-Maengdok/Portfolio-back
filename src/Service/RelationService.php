<?php

namespace App\Service;

use App\Repository\CompanyRepository;
use App\Repository\CountryRepository;
use App\Repository\CurriculumRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\LanguageRepository;
use App\Repository\LinkRepository;
use App\Repository\PictureRepository;
use App\Repository\ProjectRepository;
use App\Repository\SchoolRepository;
use App\Repository\SkillRepository;
use App\Repository\StatusRepository;
use App\Repository\TagRepository;
use App\Repository\TechnologyRepository;
use App\Repository\WorkTypeRepository;
use LogicException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RelationService {
    private $relations = [
        'company',
        'country',
        'curriculum',
        'location',
        'project',
        'school',
        'status',
    ];

    private $collections = [
        'education',
        'experience',
        'link',
        'picture',
        'project',
        'skill',
        'tag',
        'technology',
        'expectedCountry'
    ];

    public function __construct(
        private readonly CompanyRepository      $companyRepository,
        private readonly CountryRepository      $countryRepository,
        private readonly CurriculumRepository   $curriculumRepository,
        private readonly EducationRepository    $educationRepository,
        private readonly ExperienceRepository   $experienceRepository,
        private readonly LanguageRepository     $languageRepository,
        private readonly LinkRepository         $linkRepository,
        private readonly PictureRepository      $pictureRepository,
        private readonly ProjectRepository      $projectRepository,
        private readonly SchoolRepository       $schoolRepository,
        private readonly SkillRepository        $skillRepository,
        private readonly StatusRepository       $statusRepository,
        private readonly TagRepository          $tagRepository,
        private readonly TechnologyRepository   $technologyRepository,
        private readonly WorkTypeRepository     $workTypeRepository
    ) {}

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

    public function setRelations(object $entity, object $dto, ?array $relations = null): void
    {
        if (!$relations) {
            $relations = $this->relations;
        }

        foreach ($relations as $key) {
            if (!empty($dto->{$key})) {
                $this->validateRelation($dto->{$key}, $key, $entity);
            }
        }
    }

    public function setCollections(object $entity, object $dto, ?array $collections = null): void 
    {
        if (!$collections) {
            $collections = $this->collections;
        }
    
        foreach ($collections as $key) {
            if (!empty($dto->{$key})) {
                $this->validateArrayOfIds($dto->{$key}, $key, $entity);
            }
        }
    }

    private function getMethod(string $key, object $entity): string
    {
        if (!method_exists($entity, $key)) {
            throw new LogicException('Entity has no method ' . $key);
        }

        return $key;
    }

    private function getRepository(string $key): object 
    {
        return match ($key) {
            'company'           => $this->companyRepository,
            'country'           => $this->countryRepository,
            'curriculum'        => $this->curriculumRepository,
            'education'         => $this->educationRepository,
            'expectedCountry'   => $this->countryRepository,
            'experience'        => $this->experienceRepository,
            'language'          => $this->languageRepository,
            'link'              => $this->linkRepository,
            'location'          => $this->countryRepository,
            'picture'           => $this->pictureRepository,
            'project'           => $this->projectRepository,
            'school'            => $this->schoolRepository,
            'skill'             => $this->skillRepository,
            'status'            => $this->statusRepository,
            'tag'               => $this->tagRepository,
            'technology'        => $this->technologyRepository,
            'visaAvailableFor'  => $this->countryRepository,
            'workType'          => $this->workTypeRepository,
            default             => throw new NotFoundHttpException("Unknown Repository key: $key"),
        };
    }

    private function validateArrayOfIds(array $ids, string $key, object $entity): void
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

    private function validateRelation(int $id, string $key, object $entity): void
    {
        $repository = $this->getRepository($key);
        $set = $this->getMethod('set' . ucfirst($key), $entity);

        if (!$related = $repository->find($id)) {
            throw new NotFoundHttpException(ucfirst($key) . ' not found.');
        }

        $entity->$set($related);
    }
}