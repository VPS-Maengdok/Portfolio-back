<?php

namespace App\Service;

use App\DTO\SchoolDTO;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class SchoolService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(SchoolDTO $dto): School
    {
        $hydratedSchool = $this->hydrateSchool(new School(), $dto);

        $this->relationService->setRelations($hydratedSchool, $dto);

        $this->em->persist($hydratedSchool);
        $this->em->flush();

        return $hydratedSchool;
    }

    public function update(School $school, SchoolDTO $dto): School
    {
        $this->relationService->setRelations($school, $dto);

        $this->hydrateSchool($school, $dto);
        $this->em->flush();

        return $school;
    }

    public function delete(School $school): void
    {
        $this->em->remove($school);
        $this->em->flush();
    }

    private function hydrateSchool(School $school, SchoolDTO $dto): School
    {
        return $school
            ->setLabel($dto->label)
            ->setUrl($dto->url)
            ->setCity($dto->city);
    }
}
