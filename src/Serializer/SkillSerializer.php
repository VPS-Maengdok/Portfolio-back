<?php

namespace App\Serializer;

use App\Entity\Skill;
use Doctrine\Common\Collections\Collection;

final class SkillSerializer
{
    public static function list(array $skills): array
    {
        return array_map(function ($skill) {
            return SkillSerializer::details($skill);
        }, $skills);
    }

    public static function details(Skill $skill): array
    {
        return [
            'id' => $skill->getId(),
            'i18n' => Serializer::i18n($skill->getI18n()),
        ];
    }
}