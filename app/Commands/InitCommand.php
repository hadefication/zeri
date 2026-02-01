<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    protected $signature = 'init {ai? : AI type to generate after init (claude, gemini, cursor, all)} {--path= : Path to initialize .zeri directory} {--force : Force regeneration of AI files even if they exist} {--roadmap : Include project roadmap section} {--yes : Skip all questions and use defaults}';

    protected $description = 'Initialize .zeri directory structure in current project';

    private array $validAIs = ['claude', 'gemini', 'cursor', 'all'];

    public function handle()
    {
        $ai = $this->argument('ai');
        $path = $this->option('path') ?: getcwd();
        $force = $this->option('force');
        $includeRoadmap = $this->option('roadmap');
        $skipQuestions = $this->option('yes');
        $zeriPath = $path.'/.zeri';

        // Validate AI parameter if provided
        if ($ai && ! in_array(strtolower($ai), $this->validAIs)) {
            $this->error("Invalid AI type: {$ai}");
            $this->line('Valid options: '.implode(', ', $this->validAIs));

            return 1;
        }

        if (File::exists($zeriPath)) {
            if (! $force) {
                $this->error('.zeri directory already exists!');
                $this->line('Use --force to reinitialize and overwrite existing files.');

                return 1;
            }

            // Show warning and ask for confirmation
            $this->warn('⚠️  WARNING: This will remove all existing Zeri files!');
            $this->line('');
            $this->line('Files that will be removed:');
            $this->line('  📁 .zeri/ (entire directory)');

            // Check for AI files that exist
            $aiFilesToRemove = $this->findExistingAiFiles($path);
            foreach ($aiFilesToRemove as $file) {
                $this->line("  📄 {$file}");
            }

            $this->line('');

            if (! $skipQuestions && ! $this->confirm('Do you want to continue and remove these files?', false)) {
                $this->info('Operation cancelled.');

                return 0;
            }

            // Remove existing files
            $this->line('');
            $this->info('🗑️  Removing existing Zeri files...');

            // Remove .zeri directory
            File::deleteDirectory($zeriPath);
            $this->line('  ✅ Removed .zeri/');

            // Remove AI files
            foreach ($aiFilesToRemove as $file) {
                $fullPath = $path.'/'.$file;
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                    $this->line("  ✅ Removed {$file}");
                }
            }

            // Remove .cursor/rules directory if it becomes empty after removing Zeri files
            $cursorRulesDir = $path.'/.cursor/rules';
            if (File::isDirectory($cursorRulesDir)) {
                $remainingFiles = File::files($cursorRulesDir);
                if (count($remainingFiles) === 0) {
                    File::deleteDirectory($cursorRulesDir);
                    $this->line('  ✅ Removed empty .cursor/rules/');
                }
            }

            // Remove .cursor directory if it becomes completely empty
            $cursorDir = $path.'/.cursor';
            if (File::isDirectory($cursorDir)) {
                $remainingFiles = File::files($cursorDir);
                $remainingDirs = File::directories($cursorDir);
                if (count($remainingFiles) === 0 && count($remainingDirs) === 0) {
                    File::deleteDirectory($cursorDir);
                    $this->line('  ✅ Removed empty .cursor/');
                }
            }

            $this->line('');
        }

        $this->info('Initializing Zeri project structure...');

        // Create .zeri directory structure
        $directories = [
            '.zeri',
            '.zeri/specs',
            '.zeri/templates',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($path.'/'.$dir, 0755, true);
        }

        // Gather project information
        if ($skipQuestions) {
            $projectName = basename($path);
            $projectDescription = 'A new project';
            $techStack = 'To be defined';
            $currentFocus = 'Initial setup and core features';

            $this->line('Using default values:');
            $this->line("  Project name: {$projectName}");
            $this->line("  Description: {$projectDescription}");
            $this->line("  Tech stack: {$techStack}");
            $this->line("  Current focus: {$currentFocus}");
            $this->line('');
        } else {
            $projectName = $this->ask('Project name', basename($path));
            $projectDescription = $this->ask('Project description', 'A new project');
            $techStack = $this->ask('Primary tech stack', 'To be defined');
            $currentFocus = $this->ask('Current development focus', 'Initial setup and core features');
        }

        // Create roadmap section if requested
        $roadmapSection = '';
        if ($includeRoadmap) {
            $roadmapSection = "\n### Project Roadmap\n\n#### Current Sprint\nProject setup and initial development\n\n#### Next Sprint\nCore feature implementation\n\n#### Short-term Goals (2-4 weeks)\nMVP development, basic functionality\n\n#### Medium-term Goals (1-3 months)\nFeature expansion, performance optimization\n\n#### Long-term Vision (3+ months)\nFull product launch, scaling considerations\n\n#### Priority Features\nUser management, core business logic\n\n#### Technical Debt\nNone identified yet";
        }

        // Create consolidated ZERI.md file from stub
        $this->createFromStub($path, 'ZERI.md', [
            'PROJECT_NAME' => $projectName,
            'PROJECT_DESCRIPTION' => $projectDescription,
            'TECH_STACK' => $techStack,
            'ARCHITECTURE_NOTES' => 'To be documented',
            'KEY_COMPONENTS' => 'To be documented',
            'CURRENT_FOCUS' => $currentFocus,
            'ENVIRONMENT_SETUP' => 'To be documented',
            'IMPORTANT_NOTES' => 'To be documented',
            'ROADMAP_SECTION' => $roadmapSection,
            // Standards
            'CODE_STYLE' => 'To be defined',
            'NAMING_CONVENTIONS' => 'To be defined',
            'FILE_ORGANIZATION' => 'To be defined',
            'DOCUMENTATION_STANDARDS' => 'To be defined',
            'SECURITY_GUIDELINES' => 'To be defined',
            'PERFORMANCE_CONSIDERATIONS' => 'To be defined',
            // Decisions
            'RECENT_DECISIONS' => 'To be documented',
            'TECHNOLOGY_CHOICES' => $techStack,
            'DESIGN_PATTERNS' => 'To be defined',
            // Patterns
            'STANDARD_PATTERNS' => 'To be defined',
            'COMPONENT_PATTERNS' => 'To be defined',
            'DATA_HANDLING_PATTERNS' => 'To be defined',
            'ERROR_HANDLING_PATTERNS' => 'To be defined',
            'TESTING_PATTERNS' => 'To be defined',
            // Workflows
            'DEVELOPMENT_PROCESS' => 'To be defined',
            'TESTING_WORKFLOW' => 'To be defined',
            'CODE_REVIEW_PROCESS' => 'To be defined',
            'DEPLOYMENT_STEPS' => 'To be defined',
            // Specs
            'ACTIVE_SPECIFICATIONS' => '*(No specifications yet. Use `zeri add-spec <name>` to create one.)*',
        ]);

        // Create template files
        $this->createTemplateFromStub($path);

        $this->info('✅ Zeri project structure initialized successfully!');
        $this->line('');
        $this->displayFileTree($path, $ai);
        $this->line('');
        $this->line('Next steps:');
        $this->line('  • Edit .zeri/ZERI.md to match your project');
        $this->line('  • Add specifications: zeri add-spec <name>');
        if (! $ai) {
            $this->line('  • Generate AI files: zeri generate <ai>');
        }

        // Auto-generate AI files if specified
        if ($ai) {
            $this->line('');
            $this->info("🤖 Auto-generating AI files for: {$ai}");

            $exitCode = $this->call('generate', [
                'ai' => strtolower($ai),
                '--path' => $path,
                '--force' => $force,
            ]);

            if ($exitCode === 0) {
                $this->line('');
                $this->info('🎉 Project initialized and AI files generated successfully!');
            } else {
                $this->line('');
                $this->warn('⚠️  Project initialized but AI generation failed. Run manually: zeri generate '.$ai);
            }
        }

        return 0;
    }

    private function createFromStub(string $basePath, string $relativePath, array $replacements)
    {
        $stubPath = app_path('../stubs/'.str_replace('.md', '.md.stub', $relativePath));
        $targetPath = $basePath.'/.zeri/'.$relativePath;

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return;
        }

        $content = File::get($stubPath);

        foreach ($replacements as $placeholder => $value) {
            $content = str_replace('{{'.$placeholder.'}}', $value, $content);
        }

        File::put($targetPath, $content);
    }

    private function createTemplateFromStub(string $basePath)
    {
        $stubPath = app_path('../stubs/templates/spec.md.stub');
        $targetPath = $basePath.'/.zeri/templates/spec.md';

        if (! File::exists($stubPath)) {
            $this->error("Stub file not found: {$stubPath}");

            return;
        }

        $content = File::get($stubPath);

        // Keep the placeholders as-is for templates
        $replacements = [
            'SPEC_NAME' => '{{SPEC_NAME}}',
            'SPEC_OVERVIEW' => '{{SPEC_OVERVIEW}}',
            'REQUIREMENTS' => '{{REQUIREMENTS}}',
            'IMPLEMENTATION_NOTES' => '{{IMPLEMENTATION_NOTES}}',
            'TODO_ITEMS' => '{{TODO_ITEMS}}',
        ];

        foreach ($replacements as $placeholder => $value) {
            $content = str_replace('{{'.$placeholder.'}}', $value, $content);
        }

        File::put($targetPath, $content);
    }

    private function displayFileTree(string $basePath, ?string $ai): void
    {
        $this->line('📁 Project Structure:');
        $this->line('');

        $projectName = basename($basePath);
        $this->line("$projectName/");

        // Show .zeri structure
        $this->line('├── .zeri/');
        $this->line('│   ├── ZERI.md                  # Project context & AI instructions');
        $this->line('│   ├── specs/                   # Feature specifications (empty)');
        $this->line('│   └── templates/');
        $this->line('│       └── spec.md              # Specification template');

        // Show AI files if generated
        if ($ai) {
            $aiFiles = $this->getAiFiles($ai);
            $fileCount = count($aiFiles);

            for ($i = 0; $i < $fileCount; $i++) {
                $isLast = ($i === $fileCount - 1);
                $prefix = $isLast ? '└── ' : '├── ';
                $this->line($prefix.$aiFiles[$i]['display']);
            }
        }
    }

    private function getAiFiles(string $ai): array
    {
        $aiFiles = [];

        if (in_array($ai, ['claude', 'all'])) {
            $aiFiles[] = ['display' => 'CLAUDE.md                    # Reference to .zeri/ZERI.md'];
        }

        if (in_array($ai, ['gemini', 'all'])) {
            $aiFiles[] = ['display' => 'GEMINI.md                    # Reference to .zeri/ZERI.md'];
        }

        if (in_array($ai, ['cursor', 'all'])) {
            $aiFiles[] = ['display' => '.cursor/'];
            $aiFiles[] = ['display' => '│   └── rules/'];
            $aiFiles[] = ['display' => '│       └── zeri.mdc          # Reference to .zeri/ZERI.md'];
        }

        return $aiFiles;
    }

    private function findExistingAiFiles(string $basePath): array
    {
        $aiFiles = [];

        // Check for Claude file
        if (File::exists($basePath.'/CLAUDE.md')) {
            $aiFiles[] = 'CLAUDE.md';
        }

        // Check for Gemini file
        if (File::exists($basePath.'/GEMINI.md')) {
            $aiFiles[] = 'GEMINI.md';
        }

        // Check for AGENTS.md (Codex)
        if (File::exists($basePath.'/AGENTS.md')) {
            $aiFiles[] = 'AGENTS.md';
        }

        // Check for Cursor .mdc files
        if (File::exists($basePath.'/.cursor/rules/zeri.mdc')) {
            $aiFiles[] = '.cursor/rules/zeri.mdc';
        }

        // Check for old Cursor rules files
        if (File::exists($basePath.'/.cursor/rules/generate.mdc')) {
            $aiFiles[] = '.cursor/rules/generate.mdc';
        }
        if (File::exists($basePath.'/.cursor/rules/workflow.mdc')) {
            $aiFiles[] = '.cursor/rules/workflow.mdc';
        }

        return $aiFiles;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
