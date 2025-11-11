<?php

namespace App\Service;

use App\DTO\CompanyDTO;
use App\Entity\Company;
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

        $company = (new Company())
            ->setLabel($dto->label)
            ->setUrl($dto->url)
            ->setCity($dto->city)
            ->setCountry($country);

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    public function update(int $id, CompanyDTO $dto): Company
    {
        if (!$company = $this->companyRepository->find($id)) {
            throw new NotFoundHttpException('Company not found.');
        }

        if ($dto->label !== null && $dto->label !== $company->getLabel()) {
            $company->setLabel($dto->label);
        }

        if ($dto->url !== null && $dto->url !== $company->getUrl()) {
            $company->setUrl($dto->url);
        }

        if ($dto->city !== null && $dto->city !== $company->getCity()) {
            $company->setCity($dto->city);
        }

        if ($dto->country !== null && $dto->country !== $company->getCountry()->getId()) {
            if (!$country = $this->countryRepository->find($dto->country)) {
                throw new BadRequestHttpException('Invalid Country id.');
            }

            $company->setCountry($country);
        }

        $this->em->flush();

        return $company;
    }

    public function delete(Company $company): void
    {
        $this->em->remove($company);
        $this->em->flush();
    }
}
