<?php

namespace App\Serializer;

use App\Entity\Curriculum;

final class CurriculumSerializer extends Serializer
{
    public function __construct(
        private readonly CountrySerializer $countrySerializer,
        private readonly EducationSerializer $educationSerializer,
        private readonly ExperienceSerializer $experienceSerializer,
        private readonly LanguageSerializer $languageSerializer,
        private readonly LinkSerializer $linkSerializer,
        private readonly ProjectSerializer $projectSerializer,
        private readonly SkillSerializer $skillSerializer,
        private readonly TechnologySerializer $technologySerializer,
        private readonly WorkTypeSerializer $workTypeSerializer
    ) {}

    public function list(array $curriculums, array $collections): array
    {
        return array_map(function ($curriculum) use ($collections) {
            return $this->details($curriculum, $collections[$curriculum->getId()]);
        }, $curriculums);
    }

    public function details(Curriculum $curriculum, array $collections): array
    {
        return [
            'id' => $curriculum->getId(),
            'firstname' => $curriculum->getFirstname(),
            'lastname' => $curriculum->getLastname(),
            'isFreelance' => $curriculum->isFreelance(),
            'freelanceCompanyName' => $curriculum->getFreelanceCompanyName(),
            'isAvailable' => $curriculum->isAvailable(),
            'hasVisa' => $curriculum->hasVisa(),
            'i18n' => $this->i18n($curriculum->getI18n()),
            'visaAvailableFor' => $collections['visa'] ? $this->countrySerializer->list($collections['visa']) : [],
            'workType' => $collections['workType'] ? $this->workTypeSerializer->list($collections['workType']) : [],
            'link' => $collections['link'] ? $this->linkSerializer->list($collections['link']) : [],
            'experience' => $collections['experience'] ? $this->experienceSerializer->list($collections['experience']) : [],
            'education' => $collections['education'] ? $this->educationSerializer->list($collections['education']) : [],
            'technology' => $collections['technology'] ? $this->technologySerializer->list($collections['technology']) : [],
            'skill' => $collections['skill'] ? $this->skillSerializer->list($collections['skill']) : [],
            'project' => $collections['project'] ? $this->projectSerializer->list($collections['project'], 'cv') : [],
            'language' => $collections['language'] ? $this->languageSerializer->list($collections['language']) : [],
            'expectedCountry' => $collections['expectedCountry'] ? $this->countrySerializer->list($collections['expectedCountry']) : [],
            'location' => $curriculum->getLocation() ? $this->countrySerializer->details($curriculum->getLocation()) : null,
        ];
    }
}