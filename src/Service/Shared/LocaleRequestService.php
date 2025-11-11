<?php

namespace App\Service\Shared;

use App\Constant\Constant;
use App\Entity\Locale;
use App\Repository\LocaleRepository;
use Symfony\Component\HttpFoundation\Request;

final class LocaleRequestService
{
    public function __construct(private LocaleRepository $localeRepository) {}

    public function getLocale(Request $request, ?bool $exception = true): Locale | Array
    {
        $query = strtolower($request->query->get('locale'));

        if ($key = array_search($query, Constant::LOCALES, true)) {
            return $this->localeRepository->findOneByShortened(Constant::LOCALES[$key]);
        } 
        
        if ($query === 'all' && !$exception) {
            return $this->localeRepository->findAll();
        } 
                
        return $this->localeRepository->findOneByShortened(Constant::DEFAULT_LOCALE); 
        
    }
}
