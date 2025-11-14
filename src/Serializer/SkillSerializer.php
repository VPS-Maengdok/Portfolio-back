<?php

namespace App\Serializer;

use App\Entity\Skill;

final class SkillSerializer extends Serializer
{    
    public function __construct() {}

    public function list(array $skills): array
    {
        return array_map(function ($skill) {
            return $this->details($skill);
        }, $skills);
    }

    public function details(Skill $skill, ?bool $everyLocale = false): array
    {
        return [
            'id' => $skill->getId(),
            'i18n' => $everyLocale ? 
                $this->i18nComplete($skill->getI18n()->toArray()) :
                $this->i18n($skill->getI18n()),
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