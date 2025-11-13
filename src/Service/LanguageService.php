<?php

namespace App\Service;

use App\DTO\I18n\LanguageI18nDTO;
use App\DTO\LanguageDTO;
use App\Entity\Language;
use App\Entity\LanguageI18n;
use App\Entity\Locale;
use App\Repository\CurriculumRepository;
use App\Repository\LanguageI18nRepository;
use App\Repository\LanguageRepository;
use App\Repository\LocaleRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class LanguageService
{
    public function __construct(
        private LanguageRepository $languageRepository,
        private LanguageI18nRepository $languageI18nRepository,
        private ProjectRepository $projectRepository,
        private CurriculumRepository $curriculumRepository,
        private LocaleRepository $localeRepository,
        private EntityManagerInterface $em
    ) {}

    public function create(LanguageDTO $dto): Language
    {
        $hydratedLanguage = new Language();

        if ($dto->curriculum) {
            if (!$curriculum = $this->curriculumRepository->find($dto->curriculum)) {
                throw new NotFoundHttpException('Curriculum not found.');
            }

            $hydratedLanguage->setCurriculum($curriculum);
        } else {
            if(!$curriculum = $this->curriculumRepository->findOneBy([], ['createdAt' => 'DESC'])) {
                throw new NotFoundHttpException('Could not find the most recent curriculum, maybe you forgot to create a curriculum.');
            }

            $hydratedLanguage->setCurriculum($curriculum);
        }

        $this->em->persist($hydratedLanguage);

        foreach ($dto->i18n as $value) {
            if (!$locale = $this->localeRepository->find($value->locale)) {
                throw new BadRequestHttpException('Invalid Locale id.');
            }

            $i18n = $this->hydrateLanguageI18n(new LanguageI18n(), $value, $locale);
            
            $hydratedLanguage->addI18n($i18n);
            $this->em->persist($i18n);
        }

        $this->em->flush();

        return $hydratedLanguage;
    }

    public function update(int $id, LanguageDTO $dto): Language
    {
        if (!$language = $this->languageRepository->find($id)) {
            throw new NotFoundHttpException('Language not found.');
        }

        $payloadIds = [];
        foreach ($dto->i18n as $i18n) {
            if (isset($i18n->id)) {
                $payloadIds[] = $i18n->id;
            }
        }

        foreach ($language->getI18n() as $existing) {
            if (!in_array($existing->getId(), $payloadIds, true)) {
                $language->removeI18n($existing);
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
                if ($this->languageI18nRepository->findOneBy(['language' => $language, 'locale' => $locale])) {
                    throw new BadRequestHttpException('This language already has an i18n with this locale.');
                }

                $i18n = $this->hydrateLanguageI18n(new LanguageI18n(), $value, $locale);

                $language->addI18n($i18n);

            } else {
                if (!$existing = $this->languageI18nRepository->findOneBy(['id' => $value->id, 'language' => $language, 'locale' => $locale])) {
                    throw new NotFoundHttpException('Language i18n not found.');
                }

                $this->hydrateLanguageI18n($existing, $value, $locale);
            }
        }

        $this->em->flush();

        return $language;
    }

    public function delete(Language $language): void
    {
        $this->em->remove($language);
        $this->em->flush();
    }

    private function hydrateLanguageI18n(LanguageI18n $i18n, LanguageI18nDTO $dto, Locale $locale): LanguageI18n
    {
        return $i18n
            ->setLabel($dto->label)
            ->setLevel($dto->level)
            ->setLocale($locale);
    }
}
