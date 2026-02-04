<?php

namespace App\Generators;

class CursorGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return '.cursor/rules/zeri.mdc';
    }

    public function getGeneratedFiles(): array
    {
        return ['.cursor/rules/zeri.mdc'];
    }

    public function generate(bool $replace = false): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        return $this->injectReference($outputFile, $replace);
    }
}
