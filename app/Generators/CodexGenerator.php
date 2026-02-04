<?php

namespace App\Generators;

class CodexGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'AGENTS.md';
    }

    public function generate(bool $replace = false): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        return $this->injectReference($outputFile, $replace);
    }
}
