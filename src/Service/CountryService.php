<?php

namespace App\Service;

use App\DTO\CountryDTO;
use App\DTO\I18n\CountryI18nDTO;
use App\Entity\Country;
use App\Entity\CountryI18n;
use App\Entity\Locale;
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
        $country = new Country();
        $this->em->persist($country);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateCountryI18n(new CountryI18n(), $value, $locale);

            $country->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $country;
    }

    public function update(Country $country, CountryDTO $dto): Country
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($country->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $country->removeI18n($existing);
            }
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
                    throw new BadRequestHttpException('This country already has an i18n with this locale.');
                }

                $i18n = $this->hydrateCountryI18n(new CountryI18n(), $value, $locale);

                $country->addI18n($i18n);
            } else {
                if (!$existing = $this->countryI18nRepository->findOneBy(['id' => $value->id, 'country' => $country, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Country i18n not found.');
                }

                $this->hydrateCountryI18n($existing, $value, $locale);
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

    private function hydrateCountryI18n(CountryI18n $i18n, CountryI18nDTO $dto, Locale $locale): CountryI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
