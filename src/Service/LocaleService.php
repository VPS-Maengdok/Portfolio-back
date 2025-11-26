<?php

namespace App\Service;

use App\DTO\LocaleDTO;
use App\Entity\Locale;
use Doctrine\ORM\EntityManagerInterface;

final class LocaleService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    public function create(LocaleDTO $dto): Locale
    {
        $locale = $this->hydrateLocale(new Locale(), $dto);

        $this->em->persist($locale);
        $this->em->flush();

        return $locale;
    }

    public function update(Locale $locale, LocaleDTO $dto): Locale
    {
        $this->hydrateLocale($locale, $dto);
        $this->em->flush();

        return $locale;
    }

    public function delete(Locale $locale): void
    {
        $this->em->remove($locale);
        $this->em->flush();
    }

    private function hydrateLocale(Locale $locale, LocaleDTO $dto): Locale
    {
        return $locale
            ->setLabel($dto->label)
            ->setShortened($dto->shortened);
    }
}
