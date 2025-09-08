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

    public function generate(bool $force = false, bool $backup = false, bool $interactive = false): bool
    {
        $zeriFile = $this->outputPath.'/.cursor/rules/zeri.mdc';

        // Check if file needs regeneration
        if (! $this->shouldRegenerate($force, $zeriFile)) {
            return false; // No regeneration needed
        }

        // Handle existing file
        if (! $this->handleExistingFile($zeriFile, $backup, $interactive)) {
            return false; // User chose not to overwrite
        }

        $content = $this->buildUnifiedFromStub();

        // Write zeri.mdc
        return $this->writeOutput($content);
    }

    private function buildUnifiedFromStub(): string
    {
        // Use simple template approach like other generators
        return $this->createFromStub('cursor-zeri.mdc.stub', []);
    }
}
