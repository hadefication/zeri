<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class MigrateCommand extends Command
{
    protected $signature = 'migrate
                            {--path= : Path to project directory}
                            {--backup : Keep copies of old files as .bak}';

    protected $description = 'Migrate from old zeri structure (project.md + development.md) to new structure (ZERI.md)';

    public function handle()
    {
        $path = $this->option('path') ?: getcwd();
        $backup = $this->option('backup');
        $zeriPath = $path.'/.zeri';

        // Check if .zeri directory exists
        if (! File::exists($zeriPath)) {
            $this->error('.zeri directory not found. Run "zeri init" first.');

            return 1;
        }

        $hasProjectMd = File::exists($zeriPath.'/project.md');
        $hasDevelopmentMd = File::exists($zeriPath.'/development.md');
        $hasZeriMd = File::exists($zeriPath.'/ZERI.md');

        // Already migrated
        if ($hasZeriMd && ! $hasProjectMd && ! $hasDevelopmentMd) {
            $this->info('Already migrated! .zeri/ZERI.md exists and old files are removed.');

            return 0;
        }

        // Already migrated but old files still exist (partial state)
        if ($hasZeriMd && ($hasProjectMd || $hasDevelopmentMd)) {
            $this->warn('Partial migration state detected. ZERI.md exists but old files remain.');
            $this->line('Removing old files...');

            if ($hasProjectMd) {
                if ($backup) {
                    File::copy($zeriPath.'/project.md', $zeriPath.'/project.md.bak');
                    $this->line('  ✅ Backed up project.md to project.md.bak');
                }
                File::delete($zeriPath.'/project.md');
                $this->line('  ✅ Removed project.md');
            }

            if ($hasDevelopmentMd) {
                if ($backup) {
                    File::copy($zeriPath.'/development.md', $zeriPath.'/development.md.bak');
                    $this->line('  ✅ Backed up development.md to development.md.bak');
                }
                File::delete($zeriPath.'/development.md');
                $this->line('  ✅ Removed development.md');
            }

            $this->info('Migration complete!');

            return 0;
        }

        // No old files exist
        if (! $hasProjectMd && ! $hasDevelopmentMd) {
            $this->error('No old structure found (project.md or development.md).');
            $this->line('If this is a new project, run "zeri init" instead.');

            return 1;
        }

        // Perform migration
        $this->info('Migrating to new zeri structure...');
        $this->line('');

        // Read old file contents
        $projectContent = $hasProjectMd ? File::get($zeriPath.'/project.md') : '';
        $developmentContent = $hasDevelopmentMd ? File::get($zeriPath.'/development.md') : '';

        // Build the new ZERI.md content
        $zeriContent = $this->buildZeriContent($projectContent, $developmentContent);

        // Backup old files if requested
        if ($backup) {
            if ($hasProjectMd) {
                File::copy($zeriPath.'/project.md', $zeriPath.'/project.md.bak');
                $this->line('  ✅ Backed up project.md to project.md.bak');
            }
            if ($hasDevelopmentMd) {
                File::copy($zeriPath.'/development.md', $zeriPath.'/development.md.bak');
                $this->line('  ✅ Backed up development.md to development.md.bak');
            }
        }

        // Write new ZERI.md
        File::put($zeriPath.'/ZERI.md', $zeriContent);
        $this->line('  ✅ Created ZERI.md');

        // Remove old files
        if ($hasProjectMd) {
            File::delete($zeriPath.'/project.md');
            $this->line('  ✅ Removed project.md');
        }
        if ($hasDevelopmentMd) {
            File::delete($zeriPath.'/development.md');
            $this->line('  ✅ Removed development.md');
        }

        $this->line('');
        $this->info('Migration complete!');
        $this->line('');
        $this->line('Next steps:');
        $this->line('  • Review and edit .zeri/ZERI.md as needed');
        $this->line('  • Run "zeri generate <ai>" to update AI file references');

        return 0;
    }

    private function buildZeriContent(string $projectContent, string $developmentContent): string
    {
        $aiInstructions = $this->getAiInstructions();

        $content = "# Project Context\n\n";
        $content .= "This file provides context for AI assistants to help with development tasks.\n\n";
        $content .= "---\n\n";
        $content .= "## Instructions for AI Assistants\n\n";
        $content .= $aiInstructions;
        $content .= "\n\n---\n\n";
        $content .= "## Project Overview\n\n";

        // Extract content after the first heading from project.md
        $projectBody = $this->extractBodyContent($projectContent);
        $content .= $projectBody ?: "*(Migrated from project.md - edit as needed)*\n";

        $content .= "\n\n---\n\n";
        $content .= "## Development Practices\n\n";

        // Extract content after the first heading from development.md
        $developmentBody = $this->extractBodyContent($developmentContent);
        $content .= $developmentBody ?: "*(Migrated from development.md - edit as needed)*\n";

        $content .= "\n\n---\n\n";
        $content .= "## Active Specifications\n\n";
        $content .= "*(Specification references will be listed here)*\n";
        $content .= "\n\n---\n\n";
        $content .= "*Edit this file to update project context. Run `zeri generate <ai>` to update AI file references.*\n";

        return $content;
    }

    private function extractBodyContent(string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Remove the first heading line (# Title)
        $lines = explode("\n", $content);
        $inBody = false;
        $bodyLines = [];

        foreach ($lines as $line) {
            if (! $inBody && preg_match('/^#\s+/', $line)) {
                $inBody = true;

                continue;
            }
            if ($inBody) {
                $bodyLines[] = $line;
            }
        }

        return trim(implode("\n", $bodyLines));
    }

    private function getAiInstructions(): string
    {
        return <<<'INSTRUCTIONS'
When working on this project:

1. **Follow the established patterns** described in the patterns section
2. **Adhere to coding standards** outlined in the standards section
3. **Consider architectural decisions** when proposing changes
4. **Follow the development workflow** for implementation steps
5. **Reference current priorities** to align with project goals
6. **Check active specifications** for feature-specific requirements
7. **ONLY implement or start coding when files in .zeri/specs are referenced** - Do not write code without specific feature specifications

### Specification Workflow

**Always use `zeri add-spec <name>` to create new feature specifications—never create these files manually.**

```bash
# Create a new specification
zeri add-spec "feature-name"

# This creates .zeri/specs/feature-name.md with the standard template
```

#### Intelligent Spec Creation Process

When a user requests to create a specification, you MUST follow this process:

1. **Assess Completeness**: Analyze the user's request to determine if it contains sufficient detail for a complete specification.

2. **Ask Targeted Follow-up Questions**: If the request is incomplete, ask clarifying questions ONLY about missing information.

3. **Iterative Dialogue**: Continue asking follow-up questions based on user answers until you have gathered sufficient information for a complete specification.

4. **Create Specification**: Once you have comprehensive information, run `zeri add-spec "feature-name"` and populate ALL sections with detailed content based on the gathered information.

5. **Plan Implementation**: Break requirements into actionable tasks in the TODO section.

6. **Update TODOs in Real Time**: Mark each TODO item as soon as its implementation step finishes using `- [x]`.

7. **Review Before Coding**: Confirm the specification is complete with the user and explicitly ask whether it is ready for implementation before writing code.

**Critical Reminders**
- Do not implement any specification unless the user explicitly instructs you to begin coding.
- When user's request is vague (e.g., "add a new feature"), you MUST ask clarifying questions before creating the spec.
- When user's request is detailed, you may proceed directly to creating the spec without unnecessary questions.
- Update TODOs in real time—mark each checkbox immediately after its implementation step finishes.

### Key Reminders

- Always write tests for new functionality
- Follow the established code review process
- Consider performance and security implications
- Update documentation when making architectural changes
INSTRUCTIONS;
    }
}
