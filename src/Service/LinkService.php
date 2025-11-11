<?php

namespace App\Service;

use App\DTO\I18n\LinkI18nDTO;
use App\DTO\LinkDTO;
use App\Entity\Link;
use App\Entity\LinkI18n;
use App\Entity\Locale;
use App\Repository\CurriculumRepository;
use App\Repository\LinkI18nRepository;
use App\Repository\LinkRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class LinkService
{
    public function __construct(
        private LinkRepository $linkRepository,
        private LinkI18nRepository $linkI18nRepository,
        private ProjectRepository $projectRepository,
        private CurriculumRepository $curriculumRepository,
        private LocaleRepository $localeRepository,
        private EntityManagerInterface $em
    ) {}

    public function create(LinkDTO $dto): Link
    {
        if (!$dto->url && !$dto->repositoryUrl) {
            throw new BadRequestHttpException('Both urls are empty.');
        }

        if ($dto->project === null and $dto->curriculum === null) {
            throw new BadRequestHttpException('Either project or curriculum should have an id.');
        }

        $hydratedLink = $this->hydrateLink(new Link(), $dto);

        if ($dto->project) {
            if (!$project = $this->projectRepository->find($dto->project)) {
                throw new NotFoundHttpException('Project not found.');
            }

            $hydratedLink->setProject($project);
        }


        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedLink->setCurriculum($curriculum);
        }

        $this->em->persist($hydratedLink);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateLink18n(new LinkI18n(), $value, $locale);
            
            $hydratedLink->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedLink;
    }

    public function update(int $id, LinkDTO $dto): Link
    {
        if (!$link = $this->linkRepository->find($id)) {
            throw new NotFoundHttpException('Link not found.');
        }

        $hydratedLink = $this->hydrateLink($link, $dto);

        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($hydratedLink->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $hydratedLink->removeI18n($existing);
            }
        }

        foreach ($dto->i18n as $value) {
            if ($value->locale === null) {
                throw new BadRequestHttpException('Locale is required.');
            }
            
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            if (!isset($value->id)) {
                if ($this->linkI18nRepository->findOneBy(['link' => $hydratedLink, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This link already has an i18n with this locale.');
                }

                $i18n = $this->hydrateLink18n(new LinkI18n(), $value, $locale);

                $link->addI18n($i18n);

            } else {
                if (!$existing = $this->linkI18nRepository->findOneBy(['id' => $value->id, 'link' => $link, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Link i18n not found.');
                }

                $this->hydrateLink18n($existing, $value, $locale);
            }
        }

        $this->em->flush();

        return $link;
    }

    public function delete(Link $link): void
    {
        $this->em->remove($link);
        $this->em->flush();
    }

    private function hydrateLink(Link $link, LinkDTO $dto): Link
    {
        return $link
            ->setIcon($dto->icon)
            ->setIsProject($dto->isProject)
            ->setUrl($dto->url)
            ->setRepositoryUrl($dto->repositoryUrl);
    }

    private function hydrateLink18n(LinkI18n $i18n, LinkI18nDTO $dto, Locale $locale): LinkI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLocale($locale);
    }
}
