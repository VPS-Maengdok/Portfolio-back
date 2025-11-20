<?php

namespace App\Service;

use App\DTO\SchoolDTO;
use App\Entity\Country;
use App\Entity\School;
use App\Repository\CountryRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SchoolService
{
    public function __construct(
        private SchoolRepository $schoolRepository,
        private CountryRepository $countryRepository,
        private EntityManagerInterface $em
    ) {}

    public function create(SchoolDTO $dto): School
    {
        if ($dto->country && !$country = $this->countryRepository->find($dto->country)) {
            throw new BadRequestHttpException('Invalid Country id.');
        }

        $hydratedSchool = $this->hydrateSchool(new School(), $dto, $country);

        $this->em->persist($hydratedSchool);
        $this->em->flush();

        return $hydratedSchool;
    }

    public function update(School $school, SchoolDTO $dto): School
    {
        if ($dto->country && !$country = $this->countryRepository->find($dto->country)) {
            throw new BadRequestHttpException('Invalid Country id.');
        }

        $this->hydrateSchool($school, $dto, $country);
        $this->em->flush();

        return $school;
    }

    public function delete(School $school): void
    {
        $this->em->remove($school);
        $this->em->flush();
    }

    private function hydrateSchool(School $school, SchoolDTO $dto, Country $country): School
    {
        return $school
            ->setLabel($dto->label)
            ->setUrl($dto->url)
            ->setCity($dto->city)
            ->setCountry($country);
    }
}
