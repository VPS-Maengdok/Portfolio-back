<?php

namespace App\Serializer;

use App\Entity\School;

final class SchoolSerializer extends Serializer
{
    public function __construct(private readonly CountrySerializer $countrySerializer) {}

    public function list(array $schools): array
    {
        return array_map(function ($school) {
            return $this->details($school);
        }, $schools);
    }

    public function details(School $school): array
    {
        return [
            'id' => $school->getId(),
            'label' => $school->getLabel(),
            'url' => $school->getUrl(),
            'city' => $school->getCity(),
            'country' => $school->getCountry() ? $this->countrySerializer->details($school->getCountry()) : null,
        ];
    }
}