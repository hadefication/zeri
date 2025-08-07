<?php

namespace App\Generators;

class CodexGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'CODEX.md';
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
            $specsReferences = "\n**Active Specifications:**\n";
            foreach ($specs as $spec) {
                $specsReferences .= "- [@.zeri/specs/{$spec['name']}.md](.zeri/specs/{$spec['name']}.md) - {$spec['name']} specification\n";
            }
        }

        $replacements = [
            'GENERATION_DATE' => date('Y-m-d H:i:s'),
            'ACTIVE_SPECIFICATIONS_REFERENCES' => $specsReferences,
        ];

        return $this->createFromStub('CODEX.md.stub', $replacements);
    }
}
