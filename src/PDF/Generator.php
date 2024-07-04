<?php

namespace App\PDF;

use Knp\Snappy\Pdf;
use Twig\Environment;

class Generator implements GeneratorInterface
{
    public function __construct(
        private readonly Pdf $pdf,
        private readonly Environment $twig,
        protected readonly string $publicDirectory
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function generatePage(string $filename, string $view, array $data = []): string
    {
        if (is_file(sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename))) {
            unlink(sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename));
        }

        $this->pdf->generateFromHtml(
            $this->twig->render($view, $data),
            sprintf('%s/pdf/%s.pdf', $this->publicDirectory, $filename)
        );

        return sprintf('pdf/%s.pdf', $filename);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function generate(string $filename, string $view, array $data = []): string
    {
        return $this->generatePage($filename, $view, $data);
    }
}
