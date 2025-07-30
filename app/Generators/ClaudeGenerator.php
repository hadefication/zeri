<?php

namespace App\Generators;

class ClaudeGenerator extends BaseGenerator
{
    public function getOutputFileName(): string
    {
        return 'CLAUDE.md';
    }

    public function generate(bool $force = false): bool
    {
        if (! $this->shouldRegenerate($force)) {
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
        if (! empty($specs)) {
            foreach ($specs as $spec) {
                $specsList .= "- `.zeri/specs/{$spec['name']}.md` - {$spec['name']} specification\n";
            }
        } else {
            $specsList = "- *No active specifications*\n";
        }

        // Build active specifications content
        $activeSpecs = '';
        if (! empty($specs)) {
            $activeSpecs = "\n## Current Feature Specifications\n\n";
            $activeSpecs .= "The following specifications represent current or upcoming work:\n\n";
            foreach ($specs as $spec) {
                $activeSpecs .= '### '.ucfirst(str_replace('-', ' ', $spec['name']))."\n\n";
                $activeSpecs .= $spec['content']."\n\n";
            }
        }

        // Build workflows content
        $workflowsText = '';
        $workflows = ['coding.md', 'planning.md', 'debugging.md'];
        foreach ($workflows as $workflow) {
            $workflowData = $this->readFile('workflows/'.$workflow);
            if ($workflowData) {
                $title = ucfirst(str_replace('.md', '', $workflow));
                $workflowsText .= "### {$title} Workflow\n\n";
                $workflowsText .= $workflowData."\n\n";
            }
        }

        // Build project documentation
        $projectDocs = '';
        $docs = ['roadmap.md', 'decisions.md', 'patterns.md'];
        foreach ($docs as $doc) {
            $docContent = $this->readFile('project/'.$doc);
            if ($docContent) {
                $title = ucfirst(str_replace('.md', '', $doc));
                $projectDocs .= "### {$title}\n\n";
                $projectDocs .= $docContent."\n\n";
            }
        }

        $replacements = [
            'GENERATION_DATE' => date('Y-m-d H:i:s'),
            'SPECIFICATIONS_LIST' => $specsList,
            'PROJECT_CONTEXT' => $this->readFile('context.md'),
            'PROJECT_STANDARDS' => $this->readFile('standards.md'),
            'PROJECT_WORKFLOWS' => $workflowsText,
            'PROJECT_DOCUMENTATION' => $projectDocs,
            'ACTIVE_SPECIFICATIONS' => $activeSpecs,
        ];

        return $this->createFromStub('CLAUDE.md.stub', $replacements);
    }
}
