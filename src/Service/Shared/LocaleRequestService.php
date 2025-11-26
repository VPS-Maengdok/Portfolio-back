<?php

namespace App\Service\Shared;

use App\Constant\Constant;
use App\Entity\Locale;
use App\Repository\LocaleRepository;
use Symfony\Component\HttpFoundation\Request;

final class LocaleRequestService
{
    public function __construct(private LocaleRepository $localeRepository) {}

    public function getLocaleFromRequest(Request $request): Locale
    {
        $query = strtolower($request->query->get('locale'));
        $key = array_search($query, Constant::LOCALES, true);

        if ($key !== false) {
            return $this->localeRepository->findOneByShortened(Constant::LOCALES[$key]);
        }

        return $this->localeRepository->findOneByShortened(Constant::DEFAULT_LOCALE); 
    }

    public function getLocale(string $shortened): Locale
    {
        $key = array_search($shortened, Constant::LOCALES, true);

        if ($key !== false) {
            return $this->localeRepository->findOneByShortened(Constant::LOCALES[$key]);
        } 
                
        return $this->localeRepository->findOneByShortened(Constant::DEFAULT_LOCALE); 
    }
}
