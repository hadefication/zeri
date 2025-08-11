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
            $roadmapSection = "\n---\n\n## Project Roadmap\n\n### Current Sprint\nProject setup and initial development\n\n### Next Sprint\nCore feature implementation\n\n### Short-term Goals (2-4 weeks)\nMVP development, basic functionality\n\n### Medium-term Goals (1-3 months)\nFeature expansion, performance optimization\n\n### Long-term Vision (3+ months)\nFull product launch, scaling considerations\n\n### Priority Features\nUser management, core business logic\n\n### Technical Debt\nNone identified yet";
        }

        // Create files from stubs
        $this->createFromStub($path, 'project.md', [
            'PROJECT_NAME' => $projectName,
            'PROJECT_DESCRIPTION' => $projectDescription,
            'TECH_STACK' => $techStack,
            'ARCHITECTURE_NOTES' => 'To be documented',
            'KEY_COMPONENTS' => 'To be documented',
            'CURRENT_FOCUS' => $currentFocus,
            'ENVIRONMENT_SETUP' => 'To be documented',
            'IMPORTANT_NOTES' => 'To be documented',
            'ROADMAP_SECTION' => $roadmapSection,
        ]);

        // Create consolidated development file
        $this->createFromStub($path, 'development.md', [
            'PROJECT_NAME' => $projectName,
            // Standards
            'CODE_STYLE' => 'Follow established code style standards',
            'NAMING_CONVENTIONS' => 'Consistent naming conventions based on language and framework',
            'FILE_ORGANIZATION' => 'Organize by feature/domain',
            'DOCUMENTATION_STANDARDS' => 'Document all public APIs and complex business logic',
            'SECURITY_GUIDELINES' => 'Sanitize all inputs, validate data, follow security best practices',
            'PERFORMANCE_CONSIDERATIONS' => 'Optimize critical paths, cache expensive operations',
            // Decisions
            'RECENT_DECISIONS' => 'Initial technology stack selection',
            'KEY_ARCHITECTURE_DECISIONS' => 'Framework choice, database selection, deployment strategy',
            'TECHNOLOGY_CHOICES' => $techStack === 'To be defined' ? 'To be defined - choose based on team expertise and project requirements' : $techStack.' - chosen for team expertise and project requirements',
            'DESIGN_PATTERNS' => 'Appropriate patterns for the architecture and domain',
            // Patterns
            'STANDARD_PATTERNS' => 'Consistent architectural patterns',
            'COMPONENT_PATTERNS' => 'Reusable components, consistent API structure',
            'DATA_HANDLING_PATTERNS' => 'Data models, validation, serialization',
            'ERROR_HANDLING_PATTERNS' => 'Custom exceptions, error logging, user-friendly messages',
            'TESTING_PATTERNS' => 'Arrange-Act-Assert pattern, test data setup, mocking external services',
            'CONFIGURATION_PATTERNS' => 'Environment-based config, feature flags',
            'PATTERN_EXAMPLES' => 'Service layer for business logic, data transfer objects for APIs',
            // Workflows
            'DEVELOPMENT_PROCESS' => 'Feature branch workflow with code review',
            'BEFORE_STARTING' => 'Check latest main branch, create feature branch',
            'IMPLEMENTATION_STEPS' => '1. Write tests 2. Implement feature 3. Run tests 4. Code review',
            'TESTING_WORKFLOW' => 'Unit tests, integration tests, manual testing',
            'TESTING_REQUIREMENTS' => 'Write tests for all new features',
            'CODE_REVIEW_PROCESS' => 'Pull request review with at least one approval',
            'CODE_REVIEW_GUIDELINES' => 'All code must be reviewed before merge',
            'DEPLOYMENT_STEPS' => 'Deploy to staging, test, deploy to production',
            'TROUBLESHOOTING' => 'Check logs, reproduce issue, write failing test, fix, verify',
            // Planning
            'PLANNING_PROCESS' => 'Requirements gathering, technical design, estimation',
            'REQUIREMENTS_GATHERING' => 'Stakeholder interviews, user stories, acceptance criteria',
            'TECHNICAL_ANALYSIS' => 'Architecture review, dependency analysis, risk assessment',
            'DESIGN_CONSIDERATIONS' => 'User experience, performance, security, maintainability',
            'IMPLEMENTATION_PLANNING' => 'Break down into tasks, estimate effort, plan sprints',
            'RISK_ASSESSMENT' => 'Identify technical risks, mitigation strategies',
            'TIMELINE_ESTIMATION' => 'Story points, velocity tracking, buffer for unknowns',
            // Debugging
            'DEBUGGING_PROCESS' => 'Reproduce, isolate, identify root cause, fix, verify',
            'COMMON_ISSUES' => 'Database connection, configuration errors, dependency issues',
            'DEBUGGING_TOOLS' => 'Debugger, logging, profiler, monitoring tools',
            'LOG_ANALYSIS' => 'Check application logs, error logs, system logs',
            'PERFORMANCE_DEBUGGING' => 'Profiling, query analysis, resource monitoring',
            'ERROR_TRACKING' => 'Use error tracking service, categorize errors, prioritize fixes',
            'RESOLUTION_DOCUMENTATION' => 'Document solution, update runbooks, share learnings',
        ]);

        // Create template files
        $this->createFromStub($path, 'templates/spec.md', [
            'SPEC_NAME' => '{{SPEC_NAME}}',
            'SPEC_OVERVIEW' => '{{SPEC_OVERVIEW}}',
            'REQUIREMENTS' => '{{REQUIREMENTS}}',
            'IMPLEMENTATION_NOTES' => '{{IMPLEMENTATION_NOTES}}',
            'TODO_ITEMS' => '{{TODO_ITEMS}}',
        ]);

        $this->info('✅ Zeri project structure initialized successfully!');
        $this->line('');
        $this->displayFileTree($path, $ai);
        $this->line('');
        $this->line('Next steps:');
        $this->line('  • Edit .zeri files to match your project');
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

    private function displayFileTree(string $basePath, ?string $ai): void
    {
        $this->line('📁 Project Structure:');
        $this->line('');

        $projectName = basename($basePath);
        $this->line("$projectName/");

        // Show .zeri structure
        $this->line('├── .zeri/');
        $this->line('│   ├── project.md               # Project overview, tech stack & architecture');
        $this->line('│   ├── development.md           # Standards, decisions, patterns & workflows');
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
            $aiFiles[] = ['display' => 'CLAUDE.md                    # Context for Claude AI'];
        }

        if (in_array($ai, ['gemini', 'all'])) {
            $aiFiles[] = ['display' => 'GEMINI.md                    # Instructions for Gemini AI'];
        }

        if (in_array($ai, ['cursor', 'all'])) {
            $aiFiles[] = ['display' => '.cursor/'];
            $aiFiles[] = ['display' => '│   └── rules/'];
            $aiFiles[] = ['display' => '│       ├── generate.mdc      # Code generation rules'];
            $aiFiles[] = ['display' => '│       └── workflow.mdc      # Development workflow'];
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

        // Check for Cursor .mdc files
        if (File::exists($basePath.'/.cursor/rules/generate.mdc')) {
            $aiFiles[] = '.cursor/rules/generate.mdc';
        }
        if (File::exists($basePath.'/.cursor/rules/workflow.mdc')) {
            $aiFiles[] = '.cursor/rules/workflow.mdc';
        }

        // Check for old Cursor rules (for backward compatibility)
        if (File::exists($basePath.'/.cursor/rules')) {
            $aiFiles[] = '.cursor/rules';
        }

        return $aiFiles;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
