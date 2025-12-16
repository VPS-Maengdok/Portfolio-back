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
        private readonly LinkSerializer $linkSerializer,
    ) {}

    public function list(
        array $projects,
        ?string $format = 'list',
        ?int $localeId = null,
    ): array
    {
        return array_map(function ($project) use ($format, $localeId) {
            return $this->details($project, format: $format, localeId: $localeId);
        }, $projects);
    }

    public function details(
        Project $project,
        ?bool $everyLocale = false,
        ?string $format = 'details',
        ?int $localeId = null,
    ): array
    {
        return [
            'id' => $project->getId(),
            'isHidden' => $project->isHidden(),
            'i18n' => $everyLocale ?
                $this->getI18n($project->getI18n(), 'admin', $localeId) :
                $this->getI18n($project->getI18n(), $format, $localeId),
            'company' => $project->getCompany() ? $this->companySerializer->details($project->getCompany()) : null,
            'school' => $project->getSchool() ? $this->schoolSerializer->details($project->getSchool()) : null,
            'status' => $project->getStatus()
                ? $this->statusSerializer->details($project->getStatus(), false, $localeId)
                : null,
            'skill' => $project->getSkill()
                ? $this->skillSerializer->list($project->getSkill()->toArray(), $localeId)
                : [],
            'technology' => $project->getTechnology()
                ? $this->technologySerializer->list($project->getTechnology()->toArray())
                : [],
            'tag' => $project->getTag()
                ? $this->tagSerializer->list($project->getTag()->toArray(), $localeId)
                : [],
            'picture' => $project->getPicture() ? PictureSerializer::list($project->getPicture()->toArray()) : [],
            'link' => $project->getLink()
                ? $this->linkSerializer->list($project->getLink()->toArray(), $localeId)
                : [],
            'creationDate' => $project->getCreationDate() ? $project->getCreationDate()->format('Y-m-d') : null,
            'curriculum' => $project?->getCurriculum()?->getId(),
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

    private function getI18n(Collection $i18n, string $format, ?int $localeId = null): array
    {
        return match ($format) {
            'list' => $this->i18n($i18n, ['shortDescription', 'slug']),
            'details' => $this->i18n($i18n, ['description', 'slug']),
            'cv' => $this->i18n($i18n, ['cvDescription', 'slug']),
            'admin' => $this->i18nComplete($i18n->toArray(), ['description', 'shortDescription', 'cvDescription', 'slug'])
        };
    }
}
