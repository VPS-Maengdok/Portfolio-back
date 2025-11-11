<?php

namespace App\Serializer;

use App\Entity\School;

final class SchoolSerializer
{
    public static function list(array $schools): array
    {
        return array_map(function ($school) {
            return SchoolSerializer::details($school);
        }, $schools);
    }

    public static function details(School $school): array
    {
        return [
            'id' => $school->getId(),
            'label' => $school->getLabel(),
            'url' => $school->getUrl(),
            'city' => $school->getCity(),
            'country' => $school->getCountry() ? CountrySerializer::details($school->getCountry()) : null,
        ];
    }
}