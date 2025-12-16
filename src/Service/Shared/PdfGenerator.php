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
        if ($lang === 'fr') {
            $html = $this->twig->render('resume/printFR.html.twig', [
                'cv' => $curriculum,
                'collections' => $collections,
            ]);
        }

        if ($lang === 'en') {
            $html = $this->twig->render('resume/printEN.html.twig', [
                'cv' => $curriculum,
                'collections' => $collections,
            ]);
        }

        return $this->pdf->getOutputFromHtml($html, [
            'title' => sprintf('%s %s resume %s', $curriculum->getFirstname(), $curriculum->getLastname(), $lang),
            'page-size' => 'A4',
            'print-media-type' => true,
            'encoding' => 'UTF-8',
        ]);
    }
}
