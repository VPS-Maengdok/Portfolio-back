<?php

namespace App\Service\Shared;

use App\Entity\Curriculum;
use Knp\Snappy\Pdf;
use Twig\Environment;

final class PdfGenerator
{
    public function __construct(
        private Environment $twig,
        private Pdf $pdf,
    ) {}

    public function generate(Curriculum $curriculum, array $collections, string $lang): string
    {
        $template = match ($lang) {
            'fr' => 'resume/printFR.html.twig',
            'en' => 'resume/printEN.html.twig',
            'ko' => 'resume/printKO.html.twig',
            default => 'resume/printEN.html.twig',
        };

        $html = $this->twig->render($template, [
            'cv' => $curriculum,
            'collections' => $collections,
        ]);

        return $this->pdf->getOutputFromHtml($html, [
            'title' => sprintf('%s %s resume %s', $curriculum->getFirstname(), $curriculum->getLastname(), $lang),
            'page-size' => 'A4',
            'print-media-type' => true,
            'encoding' => 'UTF-8',
            'enable-local-file-access' => true,
        ]);
    }
}
