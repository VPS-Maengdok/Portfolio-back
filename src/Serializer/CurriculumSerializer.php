<?php

namespace App\Serializer;

use App\Entity\Curriculum;

final class CurriculumSerializer
{
    public static function list(array $curriculums, array $collections): array
    {
        return array_map(function ($curriculum) use ($collections) {
            return CurriculumSerializer::details($curriculum, $collections[$curriculum->getId()]);
        }, $curriculums);
    }

    public static function details(Curriculum $curriculum, array $collections): array
    {
        return [
            'id' => $curriculum->getId(),
            'firstname' => $curriculum->getFirstname(),
            'lastname' => $curriculum->getLastname(),
            'isFreelance' => $curriculum->isFreelance(),
            'freelanceCompanyName' => $curriculum->getFreelanceCompanyName(),
            'isAvailable' => $curriculum->isAvailable(),
            'hasVisa' => $curriculum->hasVisa(),
            'i18n' => Serializer::i18n($curriculum->getI18n()),
            'visaAvailableFor' => $collections['visa'] ? CountrySerializer::list($collections['visa']) : [],
            'workType' => $collections['workType'] ? WorkTypeSerializer::list($collections['workType']) : [],
            'link' => $collections['link'] ? LinkSerializer::list($collections['link']) : [],
            'experience' => $collections['experience'] ? ExperienceSerializer::list($collections['experience']) : [],
            'education' => $collections['education'] ? EducationSerializer::list($collections['education']) : [],
            'technology' => $collections['technology'] ? TechnologySerializer::list($collections['technology']) : [],
            'skill' => $collections['skill'] ? SkillSerializer::list($collections['skill']) : [],
            'project' => $collections['project'] ? ProjectSerializer::list($collections['project'], 'cv') : [],
            'language' => $collections['language'] ? LanguageSerializer::list($collections['language']) : [],
            'expectedCountry' => $collections['expectedCountry'] ? CountrySerializer::list($collections['expectedCountry']) : [],
            'location' => $curriculum->getLocation() ? CountrySerializer::details($curriculum->getLocation()) : null,
        ];
    }
}