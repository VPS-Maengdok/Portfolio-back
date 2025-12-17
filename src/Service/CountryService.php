<?php

namespace App\Service;

use App\DTO\CountryDTO;
use App\DTO\I18n\CountryI18nDTO;
use App\Entity\Country;
use App\Entity\CountryI18n;
use App\Entity\Locale;
use App\Repository\CountryI18nRepository;
use Doctrine\ORM\EntityManagerInterface;

final class CountryService
{
    public function __construct(
        private readonly I18nService                $i18nService,
        private readonly CountryI18nRepository      $countryI18nRepository,
        private readonly EntityManagerInterface     $em
    ) {}

    public function create(CountryDTO $dto): Country
    {
        $hydratedCountry = $this->hydrateCountry(new Country(), $dto);
        $this->em->persist($hydratedCountry);

        $this->i18nService->setCollectionOnCreate(
            $hydratedCountry,
            $dto->i18n,
            fn() => new CountryI18n(),
            fn(CountryI18n $i18n, CountryI18nDTO $i18nDTO, Locale $locale) => $this->hydrateCountryI18n($i18n, $i18nDTO, $locale)
        );

        $this->em->flush();

        return $hydratedCountry;
    }

    public function update(Country $country, CountryDTO $dto): Country
    {
        $this->hydrateCountry($country, $dto);
        $this->i18nService->removeCollectionOnUpdate($country, $dto->i18n);
        $this->i18nService->setCollectionOnUpdate(
            $country,
            $dto->i18n,
            fn() => new CountryI18n(),
            fn(CountryI18n $i18n, CountryI18nDTO $i18nDTO, Locale $locale) => $this->hydrateCountryI18n($i18n, $i18nDTO, $locale),
            'country',
            $this->countryI18nRepository
        );

        $this->em->flush();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->em->remove($country);
        $this->em->flush();
    }

    private function hydrateCountry(Country $country, CountryDTO $dto): Country
    {
        return $country
            ->setShortened($dto->shortened);
    }

    private function hydrateCountryI18n(CountryI18n $i18n, CountryI18nDTO $dto, Locale $locale): CountryI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
