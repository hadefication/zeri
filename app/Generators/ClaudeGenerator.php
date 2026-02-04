<?php

namespace App\Generators;

class ClaudeGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'CLAUDE.md';
    }

    public function generate(bool $replace = false): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        return $this->injectReference($outputFile, $replace);
    }
}
