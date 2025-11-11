<?php

namespace App\Serializer;

use App\Entity\Experience;
use Doctrine\Common\Collections\Collection;

final class ExperienceSerializer
{
    public static function list(array $experiences): array
    {
        return array_map(function ($experience) {
            return ExperienceSerializer::details($experience);
        }, $experiences);
    }

    public static function details(Experience $experience): array
    {
        return [
            'id' => $experience->getId(),
            'isCurrentWork' => $experience->isCurrentWork(),
            'i18n' => Serializer::i18n($experience->getI18n(), ['description', 'slug']),
            'company' => $experience->getCompany() ? CompanySerializer::details($experience->getCompany()) : null,
            'skill' => $experience->getSkill() ? SkillSerializer::list($experience->getSkill()->toArray()) : [],
            'technology' => $experience->getTechnology() ? TechnologySerializer::list($experience->getTechnology()->toArray()) : [],
            'startingDate' => $experience->getStartingDate() ? $experience->getStartingDate()->format('Y-m-d') : null,
            'endingDate' => $experience->getEndingDate() ? $experience->getEndingDate()->format('Y-m-d') : null,
        ];
    }
}