<?php

namespace App\Serializer;

use App\Entity\Company;

final class CompanySerializer extends Serializer
{
    public function __construct(private readonly CountrySerializer $countrySerializer) {}

    public function list(array $companies): array
    {
        return array_map(function ($company) {
            return $this->details($company);
        }, $companies);
    }

    public function details(Company $company): array
    {
        return [
            'id' => $company->getId(),
            'label' => $company->getLabel(),
            'url' => $company->getUrl(),
            'city' => $company->getCity(),
            'country' => $company->getCountry() ? $this->countrySerializer->details($company->getCountry()) : null,
        ];
    }

    public function create(Company $company): array
    {
        return $this->details($company);
    }

    public function update(Company $company): array
    {
        return $this->details($company);
    }
}
