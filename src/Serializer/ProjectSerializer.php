<?php

namespace App\Serializer;

use App\Entity\Project;
use Doctrine\Common\Collections\Collection;

final class ProjectSerializer extends Serializer
{
    public function __construct(
        private readonly CompanySerializer $companySerializer,
        private readonly SchoolSerializer $schoolSerializer,
        private readonly StatusSerializer $statusSerializer,
        private readonly SkillSerializer $skillSerializer,
        private readonly TechnologySerializer $technologySerializer,
        private readonly TagSerializer $tagSerializer,
    ) {}

    public function list(array $projects): array
    {
        return array_map(function ($project) {
            return $this->details($project, 'list');
        }, $projects);
    }

    public function details(Project $project, ?bool $everyLocale = false, ?string $format = 'details'): array
    {
        return [
            'id' => $project->getId(),
            'isHidden' => $project->isHidden(),
            'i18n' => $everyLocale ? 
                $this->getI18n($project->getI18n(), 'admin') :
                $this->getI18n($project->getI18n(), $format),
            'company' => $project->getCompany() ? $this->companySerializer->details($project->getCompany()) : null,
            'school' => $project->getSchool() ? $this->schoolSerializer->details($project->getSchool()) : null,
            'status' => $project->getStatus() ? $this->statusSerializer->details($project->getStatus()) : null,
            'skill' => $project->getSkill() ? $this->skillSerializer->list($project->getSkill()->toArray()) : [],
            'technology' => $project->getTechnology() ? $this->technologySerializer->list($project->getTechnology()->toArray()) : [],
            'tag' => $project->getTag() ? $this->tagSerializer->list($project->getTag()->toArray()) : [],
            'picture' => $project->getPicture() ? PictureSerializer::list($project->getPicture()->toArray()) : [],
            'creationDate' => $project->getCreationDate() ? $project->getCreationDate()->format('Y-m-d') : null,
        ];
    }

    public function create(Project $project): array
    {
        return $this->details($project, true);
    }

    public function update(Project $project): array
    {
        return $this->details($project, true);
    }

    private function getI18n(Collection $i18n, string $format): array
    {
        return match ($format) {
            'list' => $this->i18n($i18n, ['shortDescription', 'slug']),
            'details' => $this->i18n($i18n, ['description', 'slug']),
            'cv' => $this->i18n($i18n, ['cvDescription', 'slug']),
            'admin' => $this->i18nComplete($i18n->toArray(), ['description', 'shortDescription', 'cvDescription', 'slug'])
        };
    }
}