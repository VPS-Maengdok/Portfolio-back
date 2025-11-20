<?php

namespace App\Service;

use App\DTO\I18n\TagI18nDTO;
use App\DTO\TagDTO;
use App\Entity\Tag;
use App\Entity\TagI18n;
use App\Entity\Locale;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\TagI18nRepository;
use App\Repository\TagRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Repository\TechnologyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TagService extends Service
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly TagI18nRepository $tagI18nRepository,
        private readonly LocaleRepository $localeRepository,
        private readonly EntityManagerInterface $em,
        private readonly ProjectRepository $projectRepository,
        private readonly ExperienceRepository $experienceRepository,
        private readonly EducationRepository $educationRepository,
        private readonly SkillRepository $skillRepository,
        private readonly TechnologyRepository $technologyRepository
    ) {
        parent::__construct($projectRepository, $experienceRepository, $educationRepository, $skillRepository, $technologyRepository);
    }

    public function create(TagDTO $dto): Tag
    {
        $hydratedTag = new Tag();

        if ($dto->project) {
            $this->validateArrayOfIdsOnCreate($dto->project, 'project', $hydratedTag);
        }

        $this->em->persist($hydratedTag);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateTagI18n(new TagI18n(), $value, $locale);
            
            $hydratedTag->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedTag;
    }

    public function update(Tag $tag, TagDTO $dto): Tag
    {
        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($tag->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $tag->removeI18n($existing);
            }
        }

        if ($dto->project) {
            $this->validateArrayOfIdsOnUpdate($dto->project, 'project', $tag);
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                $i18n = $this->hydrateTagI18n(new TagI18n(), $value, $locale);
                $tag->addI18n($i18n);
            } else {
                if (!$existing = $this->tagI18nRepository->findOneBy(['id' => $value->id, 'tag' => $tag, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Tag i18n not found.');
                }

                $this->hydrateTagI18n($existing, $value, $locale);
            }
        }

        $this->em->flush();

        return $tag;
    }

    public function delete(Tag $tag): void
    {
        $this->em->remove($tag);
        $this->em->flush();
    }

    private function hydrateTagI18n(TagI18n $i18n, TagI18nDTO $dto, Locale $locale): TagI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
