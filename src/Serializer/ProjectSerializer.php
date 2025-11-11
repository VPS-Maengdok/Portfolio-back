<?php

namespace App\Serializer;

use App\Entity\Project;
use Doctrine\Common\Collections\Collection;

final class ProjectSerializer
{
    public static function list(array $projects): array
    {
        return array_map(function ($project) {
            return ProjectSerializer::details($project, 'list');
        }, $projects);
    }

    public static function details(Project $project, ?string $format = 'details'): array
    {
        return [
            'id' => $project->getId(),
            'isHidden' => $project->isHidden(),
            'i18n' => ProjectSerializer::getI18n($project->getI18n(), $format),
            'company' => $project->getCompany() ? CompanySerializer::details($project->getCompany()) : null,
            'school' => $project->getSchool() ? SchoolSerializer::details($project->getSchool()) : null,
            'status' => $project->getStatus() ? StatusSerializer::details($project->getStatus()) : null,
            'skill' => $project->getSkill() ? SkillSerializer::list($project->getSkill()->toArray()) : [],
            'technology' => $project->getTechnology() ? TechnologySerializer::list($project->getTechnology()->toArray()) : [],
            'tag' => $project->getTag() ? TagSerializer::list($project->getTag()->toArray()) : [],
            'picture' => $project->getPicture() ? PictureSerializer::list($project->getPicture()->toArray()) : [],
            'creationDate' => $project->getCreationDate() ? $project->getCreationDate()->format('Y-m-d') : null,
        ];
    }

    private static function getI18n(Collection $i18n, ?string $format = 'list'): array
    {
        return match ($format) {
            'list' => Serializer::i18n($i18n, ['short_description', 'slug']),
            'details' => Serializer::i18n($i18n, ['description', 'slug']),
            'cv' => Serializer::i18n($i18n, ['cvDescription', 'slug']),
        };
    }
}