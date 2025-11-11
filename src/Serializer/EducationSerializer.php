<?php

namespace App\Serializer;

use App\Entity\Education;
use Doctrine\Common\Collections\Collection;

final class EducationSerializer
{
    public static function list(array $educations): array
    {
        return array_map(function ($education) {
            return EducationSerializer::details($education);
        }, $educations);
    }

    public static function details(Education $education): array
    {
        return [
            'id' => $education->getId(),
            'i18n' => Serializer::i18n($education->getI18n(), ['diploma', 'slug']),
            'school' => $education->getSchool() ? SchoolSerializer::details($education->getSchool()) : null,
            'skill' => $education->getSkill() ? SkillSerializer::list($education->getSkill()->toArray()) : [],
            'technology' => $education->getTechnology() ? TechnologySerializer::list($education->getTechnology()->toArray()) : [],
            'startingDate' => $education->getStartingDate() ? $education->getStartingDate()->format('Y-m-d') : null,
            'endingDate' => $education->getEndingDate() ? $education->getEndingDate()->format('Y-m-d') : null,
        ];
    }
}