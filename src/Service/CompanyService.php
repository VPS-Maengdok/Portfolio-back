<?php

namespace App\Service;

use App\DTO\CompanyDTO;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

final class CompanyService
{
    public function __construct(
        private readonly RelationService            $relationService,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(CompanyDTO $dto): Company
    {
        $hydratedCompany = $this->hydrateCompany(new Company(), $dto);
        $this->relationService->setRelations($hydratedCompany, $dto);

        $this->em->persist($hydratedCompany);
        $this->em->flush();

        return $hydratedCompany;
    }

    public function update(Company $company, CompanyDTO $dto): Company
    {
        $this->hydrateCompany($company, $dto);
        $this->relationService->setRelations($company, $dto);

        $this->em->flush();

        return $company;
    }

    public function delete(Company $company): void
    {
        $this->em->remove($company);
        $this->em->flush();
    }

    private function hydrateCompany(Company $company, CompanyDTO $dto): Company
    {
        return $company
            ->setLabel($dto->label)
            ->setUrl($dto->url)
            ->setCity($dto->city);
    }
}
