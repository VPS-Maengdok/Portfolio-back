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
            return $this->details($curriculum);
        }, $curriculums);
    }

    public function details(Curriculum $curriculum, ?array $collections = null, ?bool $everyLocale = false): array
    {
        $base = [
            'id' => $curriculum->getId(),
            'firstname' => $curriculum->getFirstname(),
            'lastname' => $curriculum->getLastname(),
            'isFreelance' => $curriculum->isFreelance(),
            'freelanceCompanyName' => $curriculum->getFreelanceCompanyName(),
            'isAvailable' => $curriculum->isAvailable(),
            'hasVisa' => $curriculum->hasVisa(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($curriculum->getI18n()->toArray(), ['slug']) :
                $this->i18n($curriculum->getI18n(), ['slug']),
            'location' => $curriculum->getLocation() ? 
                $this->countrySerializer->details($curriculum->getLocation()) : 
                null,
        ];

        if (isset($collections)) {
            $collections = [
                'visaAvailableFor' => $this->countrySerializer->list($collections['visa']),
                'workType' => $this->workTypeSerializer->list($collections['workType']),
                'link' => $this->linkSerializer->list($collections['link']),
                'experience' => $this->experienceSerializer->list($collections['experience']),
                'education' => $this->educationSerializer->list($collections['education']),
                'technology' => $this->technologySerializer->list($collections['technology']),
                'skill' => $this->skillSerializer->list($collections['skill']),
                'project' => $this->projectSerializer->list($collections['project'], 'cv'),
                'language' => $this->languageSerializer->list($collections['language']),
                'expectedCountry' => $this->countrySerializer->list($collections['expectedCountry']),
            ];
        } else {
            $collections = [
                'visaAvailableFor' => $this->countrySerializer->list($curriculum->getVisaAvailableFor()->toArray()),
                'workType' => $this->workTypeSerializer->list($curriculum->getWorkType()->toArray()),
                'link' => $this->linkSerializer->list($curriculum->getLink()->toArray()),
                'experience' => $this->experienceSerializer->list($curriculum->getExperience()->toArray()),
                'education' => $this->educationSerializer->list($curriculum->getEducation()->toArray()),
                'technology' => $this->technologySerializer->list($curriculum->getTechnology()->toArray()),
                'skill' => $this->skillSerializer->list($curriculum->getSkill()->toArray()),
                'project' => $this->projectSerializer->list($curriculum->getProject()->toArray(), 'cv'),
                'language' => $this->languageSerializer->list($curriculum->getLanguage()->toArray()),
                'expectedCountry' => $this->countrySerializer->list($curriculum->getExpectedCountry()->toArray()),
            ];
        }

        return array_merge($base, $collections);
    }

    public function create(Curriculum $curriculum): array
    {
        return $this->details($curriculum, everyLocale: true);
    }

    public function update(Curriculum $curriculum): array
    {
        return $this->details($curriculum, everyLocale: true);
    }
}