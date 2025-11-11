<?php

namespace App\Service;

use App\DTO\CountryDTO;
use App\Entity\Country;
use App\Entity\CountryI18n;
use App\Repository\CountryI18nRepository;
use App\Repository\CountryRepository;
use App\Repository\LocaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CountryService
{
    public function __construct(
        private CountryRepository $countryRepository,
        private CountryI18nRepository $countryI18nRepository,
        private LocaleRepository $localeRepository,
        private EntityManagerInterface $em
    ) {}

    public function create(CountryDTO $dto): Country
    {
        $country = (new Country());
        $this->em->persist($country);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = (new CountryI18n())
            ->setLabel($value->label)
            ->setLocale($locale);
            
            $country->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $country;
    }

    public function update(int $id, CountryDTO $dto): Country
    {
        if (!$country = $this->countryRepository->find($id)) {
            throw new NotFoundHttpException('Country not found.');
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                if ($this->countryI18nRepository->findOneBy(['country' => $country, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This Country already has an i18n with this locale.');
                }

                $i18n = (new CountryI18n())
                    ->setLabel($value->label)
                    ->setLocale($locale);

                $country->addI18n($i18n);
            } else {
                if (!$existing = $this->countryI18nRepository->findOneBy(['id' => $value->id, 'country' => $country, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Country i18n not found.');
                }

                if ($existing->getLabel() !== $value->label) {
                    $existing->setLabel($value->label);
                }

                if ($existing->getLocale()->getId() !== $locale) {
                    $existing->setLocale($locale);
                }
            }
        }

        $this->em->flush();

        return $country;
    }

    public function delete(Country $country): void
    {
        $this->em->remove($country);
        $this->em->flush();
    }
}
