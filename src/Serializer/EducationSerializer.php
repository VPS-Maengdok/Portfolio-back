<?php

namespace App\Serializer;

use App\Entity\Education;

final class EducationSerializer extends Serializer
{
    public function __construct(
        private readonly SchoolSerializer $schoolSerializer,
        private readonly SkillSerializer $skillSerializer,
        private readonly TechnologySerializer $technologySerializer
    ) {}

    public function list(array $educations, ?int $localeId = null): array
    {
        return array_map(function ($education) use ($localeId) {
            return $this->details($education, false, $localeId);
        }, $educations);
    }

    public function details(
        Education $education,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array {
        return [
            'id' => $education->getId(),
            'i18n' => $everyLocale ?
                $this->i18nComplete($education->getI18n()->toArray(), ['diploma', 'description', 'slug']) :
                $this->i18n($education->getI18n(), ['diploma', 'description', 'slug'], $localeId),
            'school' => $education->getSchool() ? $this->schoolSerializer->details($education->getSchool()) : null,
            'skill' => $education->getSkill() ? $this->skillSerializer->list($education->getSkill()->toArray()) : [],
            'technology' => $education->getTechnology() ? $this->technologySerializer->list($education->getTechnology()->toArray()) : [],
            'startingDate' => $education->getStartingDate() ? $education->getStartingDate()->format('Y-m-d') : null,
            'endingDate' => $education->getEndingDate() ? $education->getEndingDate()->format('Y-m-d') : null,
            'curriculum' => $education->getCurriculum()?->getId(),
        ];
    }

    public function create(Education $education): array
    {
        return $this->details($education, true);
    }

    public function update(Education $education): array
    {
        return $this->details($education, true);
    }
}
