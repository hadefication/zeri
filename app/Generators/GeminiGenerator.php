<?php

namespace App\Generators;

class GeminiGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'GEMINI.md';
    }

    public function generate(bool $force = false, bool $backup = false, bool $interactive = false): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        if (! $this->shouldRegenerate($force, $outputFile)) {
            return false; // No regeneration needed
        }

        if (! $this->handleExistingFile($outputFile, $backup, $interactive)) {
            return false; // User chose not to overwrite
        }

        $content = $this->buildFromStub();

        return $this->writeOutput($content);
    }

    private function buildFromStub(): string
    {
        // Build specifications references
        $specs = $this->getSpecifications();
        $specsReferences = '';
        if (! empty($specs)) {
            $specsReferences = "\n**ACTIVE SPECIFICATIONS:**\n";
            foreach ($specs as $spec) {
                $specName = strtoupper(str_replace('-', ' ', $spec['name']));
                $specsReferences .= "- [@.zeri/specs/{$spec['name']}.md](.zeri/specs/{$spec['name']}.md) → {$specName} SPECIFICATION\n";
            }
        }

        $replacements = [
            'GENERATION_DATE' => date('Y-m-d H:i:s'),
            'ACTIVE_SPECIFICATIONS_REFERENCES' => $specsReferences,
        ];

        return $this->createFromStub('GEMINI.md.stub', $replacements);
    }
}
