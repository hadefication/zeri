<?php

namespace App\Generators;

class GeminiGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'GEMINI.md';
    }

    public function generate(bool $replace = false): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        return $this->injectReference($outputFile, $replace);
    }
}
