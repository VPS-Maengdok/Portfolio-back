<?php

namespace App\Serializer;

use App\Entity\Skill;

final class SkillSerializer extends Serializer
{    
    public function __construct() {}

    public function list(array $skills, ?int $localeId = null): array
    {
        return array_map(function ($skill) use ($localeId) {
            return $this->details($skill, false, $localeId);
        }, $skills);
    }

    public function details(
        Skill $skill,
        ?bool $everyLocale = false,
        ?int $localeId = null,
    ): array
    {
        return [
            'id' => $skill->getId(),
            'i18n' => $everyLocale ? 
                $this->i18nComplete($skill->getI18n()->toArray(), [], $localeId) :
                $this->i18n($skill->getI18n(), [], $localeId),
            'curriculum' => $skill->getCurriculum()?->getId(),
        ];
    }

    public function create(Skill $skill): array
    {
        return $this->details($skill, true);
    }

    public function update(Skill $skill): array
    {
        return $this->details($skill, true);
    }
}
