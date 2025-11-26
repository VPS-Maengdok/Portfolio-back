<?php

namespace App\Service;

use App\DTO\TechnologyDTO;
use App\Entity\Technology;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TechnologyService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(TechnologyDTO $dto): Technology
    {
        $hydratedTechnology = $this->hydrateTechnology(new Technology(), $dto);

        $this->relationService->setCurriculum($hydratedTechnology, $dto);
        $this->relationService->setCollections($hydratedTechnology, $dto);

        $this->em->persist($hydratedTechnology);
        $this->em->flush();

        return $hydratedTechnology;
    }

    public function update(Technology $technology, TechnologyDTO $dto): Technology
    {
        $this->relationService->setCurriculum($technology, $dto);
        $this->relationService->setCollections($technology, $dto);

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
