<?php

namespace App\Service;

use App\DTO\CompanyDTO;
use App\Entity\Company;
use App\Entity\Country;
use App\Repository\CompanyRepository;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CompanyService
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private CountryRepository $countryRepository,
        private EntityManagerInterface $em
    ) {}

    public function create(CompanyDTO $dto): Company
    {
        if (!$country = $this->countryRepository->find($dto->country)) {
            throw new BadRequestHttpException('Invalid Country id.');
        }

        $company = $this->hydrateCompany(new Company(), $dto, $country);

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    public function update(Company $company, CompanyDTO $dto): Company
    {
        if (!$country = $this->countryRepository->find($dto->country)) {
            throw new BadRequestHttpException('Invalid Country id.');
        }

        $this->hydrateCompany($company, $dto, $country);
        $this->em->flush();

        return $company;
    }

    public function delete(Company $company): void
    {
        $this->em->remove($company);
        $this->em->flush();
    }

    private function hydrateCompany(Company $company, CompanyDTO $dto, Country $country): Company
    {
        return $company
            ->setLabel($dto->label)
            ->setUrl($dto->url)
            ->setCity($dto->city)
            ->setCountry($country);
    }
}
