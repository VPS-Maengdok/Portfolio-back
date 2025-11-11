<?php

namespace App\Serializer;

use App\Entity\Company;

final class CompanySerializer
{
    public static function list(array $companies): array
    {
        return array_map(function ($company) {
            return CompanySerializer::details($company);
        }, $companies);
    }

    public static function details(Company $company): array
    {
        return [
            'id' => $company->getId(),
            'label' => $company->getLabel(),
            'url' => $company->getUrl(),
            'city' => $company->getCity(),
            'country' => $company->getCountry() ? CountrySerializer::details($company->getCountry()) : null,
        ];
    }

    public static function create(Company $company): array
    {
        return CompanySerializer::details($company);
    }

    public static function update(Company $company): array
    {
        return CompanySerializer::details($company);
    }
}
