<?php

namespace App\PDF;

interface GeneratorInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function generate(string $filename, string $view, array $data = []): string;
}
