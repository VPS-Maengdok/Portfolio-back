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

    public function list(array $curriculums, array $collections, ?int $localeId = null): array
    {
        return array_map(function ($curriculum) use ($collections, $localeId) {
            return $this->details(
                $curriculum,
                $collections[$curriculum->getId()],
                false,
                $localeId,
            );
        }, $curriculums);
    }

    public function details(
        Curriculum $curriculum,
        ?array $collections = null,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array {
        $base = [
            'id' => $curriculum->getId(),
            'firstname' => $curriculum->getFirstname(),
            'lastname' => $curriculum->getLastname(),
            'city' => $curriculum->getCity(),
            'isFreelance' => $curriculum->isFreelance(),
            'freelanceCompanyName' => $curriculum->getFreelanceCompanyName(),
            'isAvailable' => $curriculum->isAvailable(),
            'hasVisa' => $curriculum->hasVisa(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($curriculum->getI18n()->toArray(), ['slug'], $localeId) :
                $this->i18n($curriculum->getI18n(), ['slug'], $localeId),
            'location' => $curriculum->getLocation() ?
                $this->countrySerializer->details($curriculum->getLocation(), false, $localeId) :
                null,
        ];

        if (isset($collections)) {
            $collections = [
                'visaAvailableFor' => $this->countrySerializer->list($collections['visa'], $localeId),
                'workType' => $this->workTypeSerializer->list($collections['workType'], $localeId),
                'link' => $this->linkSerializer->list($collections['link'], $localeId),
                'experience' => $this->experienceSerializer->list($collections['experience'], $localeId),
                'education' => $this->educationSerializer->list($collections['education'], $localeId),
                'technology' => $this->technologySerializer->list($collections['technology']),
                'skill' => $this->skillSerializer->list($collections['skill'], $localeId),
                'project' => $this->projectSerializer->list($collections['project'], 'cv', $localeId),
                'language' => $this->languageSerializer->list($collections['language'], $localeId),
                'expectedCountry' => $this->countrySerializer->list($collections['expectedCountry'], $localeId),
            ];
        } else {
            $collections = [
                'visaAvailableFor' => $this->countrySerializer->list($curriculum->getVisaAvailableFor()->toArray(), $localeId),
                'workType' => $this->workTypeSerializer->list($curriculum->getWorkType()->toArray(), $localeId),
                'link' => $this->linkSerializer->list($curriculum->getLink()->toArray(), $localeId),
                'experience' => $this->experienceSerializer->list($curriculum->getExperience()->toArray(), $localeId),
                'education' => $this->educationSerializer->list($curriculum->getEducation()->toArray(), $localeId),
                'technology' => $this->technologySerializer->list($curriculum->getTechnology()->toArray()),
                'skill' => $this->skillSerializer->list($curriculum->getSkill()->toArray(), $localeId),
                'project' => $this->projectSerializer->list($curriculum->getProject()->toArray(), 'cv', $localeId),
                'language' => $this->languageSerializer->list($curriculum->getLanguage()->toArray(), $localeId),
                'expectedCountry' => $this->countrySerializer->list($curriculum->getExpectedCountry()->toArray(), $localeId),
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
