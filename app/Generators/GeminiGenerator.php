<?php

namespace App\Generators;

class GeminiGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'GEMINI.md';
    }

    public function generate(bool $force = false): bool
    {
        if (!$this->shouldRegenerate($force)) {
            return false; // No regeneration needed
        }

        $content = $this->buildFromStub();
        return $this->writeOutput($content);
    }

    private function buildFromStub(): string
    {
        // Build specifications list
        $specs = $this->getSpecifications();
        $specsList = '';
        if (!empty($specs)) {
            foreach ($specs as $spec) {
                $specName = strtoupper(str_replace('-', ' ', $spec['name']));
                $specsList .= "- `.zeri/specs/{$spec['name']}.md` → {$specName} SPECIFICATION\n";
            }
        } else {
            $specsList = "- *NO ACTIVE SPECIFICATIONS*\n";
        }

        // Build active specifications content
        $activeSpecs = '';
        if (!empty($specs)) {
            $activeSpecs = "\n## ACTIVE SPECIFICATIONS\n\n";
            
            foreach ($specs as $spec) {
                $specName = strtoupper(str_replace('-', ' ', $spec['name']));
                $activeSpecs .= "### {$specName}\n\n";
                $activeSpecs .= $this->formatForGemini($spec['content']) . "\n\n";
            }
        }

        // Build workflow content
        $workflowsText = "";
        $workflows = ['coding.md', 'planning.md', 'debugging.md'];
        foreach ($workflows as $workflow) {
            $workflowData = $this->readFile('workflows/' . $workflow);
            if ($workflowData) {
                $title = strtoupper(str_replace('.md', '', $workflow));
                $workflowsText .= "### {$title} PROTOCOL\n\n";
                $workflowsText .= $this->formatForGemini($workflowData) . "\n\n";
            }
        }

        $replacements = [
            'GENERATION_DATE' => date('Y-m-d H:i:s'),
            'SPECIFICATIONS_LIST' => trim($specsList),
            'PROJECT_CONTEXT' => $this->formatForGemini($this->readFile('context.md')),
            'PROJECT_STANDARDS' => $this->formatForGemini($this->readFile('standards.md')),
            'PROJECT_WORKFLOWS' => trim($workflowsText),
            'PROJECT_ROADMAP' => $this->formatForGemini($this->readFile('project/roadmap.md')),
            'PROJECT_DECISIONS' => $this->formatForGemini($this->readFile('project/decisions.md')),
            'PROJECT_PATTERNS' => $this->formatForGemini($this->readFile('project/patterns.md')),
            'ACTIVE_SPECIFICATIONS' => $activeSpecs
        ];

        return $this->createFromStub('GEMINI.md.stub', $replacements);
    }

    private function formatForGemini(string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        // Convert to more directive format for Gemini
        $lines = explode("\n", $content);
        $formatted = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                $formatted[] = "";
                continue;
            }
            
            // Convert headers to UPPERCASE
            if (strpos($line, '#') === 0) {
                $level = substr_count($line, '#');
                $text = trim(str_replace('#', '', $line));
                $formatted[] = str_repeat('#', $level) . ' ' . strtoupper($text);
            } else {
                $formatted[] = $line;
            }
        }
        
        return implode("\n", $formatted);
    }
}