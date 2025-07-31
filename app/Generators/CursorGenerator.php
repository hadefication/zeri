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
        $specs = $this->getSpecifications();
        $context = $this->readFile('project.md');
        $development = $this->readFile('development.md');

        // Build specification references
        $specReferences = '';
        if (! empty($specs)) {
            foreach ($specs as $spec) {
                $specReferences .= "\n@.zeri/specs/{$spec['name']}.md";
            }
        }

        // Extract tech stack rule
        $techStack = $this->extractTechStack($context);
        $techStackRule = $techStack ? "- Use {$techStack} as primary technology stack\n" : '';

        // Extract code style rules
        $codeStyleRules = '';
        if ($development) {
            $rules = $this->extractCodeStyleRules($development);
            foreach ($rules as $rule) {
                $codeStyleRules .= "- {$rule}\n";
            }
        }

        // Build file organization section
        $fileOrgSection = '';
        if ($development) {
            $orgRules = $this->extractOrganizationRules($development);
            if (! empty($orgRules)) {
                $fileOrgSection = "## File Organization\n";
                foreach ($orgRules as $rule) {
                    $fileOrgSection .= "- {$rule}\n";
                }
                $fileOrgSection .= "\n";
            }
        }

        // Build common patterns section
        $commonPatternsSection = '';
        if ($development) {
            $commonPatterns = $this->extractCommonPatterns($development);
            if (! empty($commonPatterns)) {
                $commonPatternsSection = "\n## Common Patterns\n";
                foreach ($commonPatterns as $pattern) {
                    $commonPatternsSection .= "- {$pattern}\n";
                }
            }
        }

        // Build current work section
        $currentWorkSection = '';
        if (! empty($specs)) {
            $currentWorkSection = "\n# Current Work\n\nActive specifications:";
            foreach ($specs as $spec) {
                $summary = $this->extractSpecSummary($spec['content']);
                $currentWorkSection .= "\n- {$spec['name']}: {$summary}";
            }
        }

        $replacements = [
            'SPECIFICATION_REFERENCES' => $specReferences,
            'TECH_STACK_RULE' => $techStackRule,
            'CODE_STYLE_RULES' => $codeStyleRules,
            'FILE_ORGANIZATION_SECTION' => $fileOrgSection,
            'COMMON_PATTERNS_SECTION' => $commonPatternsSection,
            'CURRENT_WORK_SECTION' => $currentWorkSection,
        ];

        return $this->createFromStub('cursor-zeri.mdc.stub', $replacements);
    }

    private function extractTechStack(string $content): string
    {
        // Extract tech stack from context
        if (preg_match('/Tech Stack[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    private function extractCodeStyleRules(string $content): array
    {
        $rules = [];

        // Extract specific code style rules
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^[-*]\s*(.+)/', $line, $matches)) {
                $rule = trim($matches[1]);
                if (strlen($rule) > 10 && strlen($rule) < 100) {
                    $rules[] = $rule;
                }
            }
        }

        // Add default rules if none found
        if (empty($rules)) {
            $rules = [
                'Follow PSR-12 coding standards',
                'Use descriptive variable names',
                'Keep functions small and focused',
                'Comment complex logic',
            ];
        }

        return array_slice($rules, 0, 5); // Limit to 5 rules for conciseness
    }

    private function extractOrganizationRules(string $content): array
    {
        $rules = [];

        // Look for file organization patterns
        if (strpos($content, 'organize') !== false || strpos($content, 'structure') !== false) {
            $rules[] = 'Organize files by feature/domain';
            $rules[] = 'Keep related files together';
            $rules[] = 'Use consistent naming conventions';
        }

        return $rules;
    }

    private function extractSpecSummary(string $content): string
    {
        // Extract a brief summary from the specification
        if (preg_match('/Overview[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/Description[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        // Fallback to first line that's not a header
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (! empty($line) && ! str_starts_with($line, '#') && strlen($line) > 10) {
                return substr($line, 0, 80).(strlen($line) > 80 ? '...' : '');
            }
        }

        return 'New feature specification';
    }

    private function extractCommonPatterns(string $content): array
    {
        $patterns = [];

        // Look for pattern examples in the content
        if (preg_match_all('/pattern[:\s]*(.+?)(?:\n|$)/i', $content, $matches)) {
            foreach ($matches[1] as $pattern) {
                $pattern = trim($pattern);
                if (strlen($pattern) > 5 && strlen($pattern) < 80) {
                    $patterns[] = $pattern;
                }
            }
        }

        return array_slice($patterns, 0, 3); // Limit for conciseness
    }
}
