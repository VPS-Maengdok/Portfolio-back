<?php

namespace App\Serializer;

use App\Entity\Experience;

final class ExperienceSerializer extends Serializer
{
    public function __construct(
        private readonly CompanySerializer $companySerializer,
        private readonly SkillSerializer $skillSerializer,
        private readonly TechnologySerializer $technologySerializer
    ) {}

    public function list(array $experiences): array
    {
        return array_map(function ($experience) {
            return $this->details($experience);
        }, $experiences);
    }

    public function details(Experience $experience, ?bool $everyLocale = false): array
    {
        return [
            'id' => $experience->getId(),
            'isCurrentWork' => $experience->isCurrentWork(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($experience->getI18n()->toArray(), ['description', 'slug']) :
                $this->i18n($experience->getI18n(), ['description', 'slug']),
            'company' => $experience->getCompany() ? $this->companySerializer->details($experience->getCompany()) : null,
            'skill' => $experience->getSkill() ? $this->skillSerializer->list($experience->getSkill()->toArray()) : [],
            'technology' => $experience->getTechnology() ? $this->technologySerializer->list($experience->getTechnology()->toArray()) : [],
            'startingDate' => $experience->getStartingDate() ? $experience->getStartingDate()->format('Y-m-d') : null,
            'endingDate' => $experience->getEndingDate() ? $experience->getEndingDate()->format('Y-m-d') : null,
            'curriculum' => $experience->getCurriculum()?->getId(),
        ];
    }

    public function create(Experience $experience): array
    {
        return $this->details($experience, true);
    }

    public function update(Experience $experience): array
    {
        return $this->details($experience, true);
    }
}