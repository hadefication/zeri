<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
protected $signature = 'init {--path= : Path to initialize .zeri directory}';

protected $description = 'Initialize .zeri directory structure in current project';

public function handle()
{
$path = $this->option('path') ?: getcwd();
$zeriPath = $path . '/.zeri';

if (File::exists($zeriPath)) {
$this->error('.zeri directory already exists!');
return 1;
}

$this->info('Initializing Zeri project structure...');


$directories = [
'.zeri',
'.zeri/workflows',
'.zeri/project', 
'.zeri/specs',
'.zeri/templates'
];

foreach ($directories as $dir) {
File::makeDirectory($path . '/' . $dir, 0755, true);
}


$projectName = $this->ask('Project name', basename($path));
$projectDescription = $this->ask('Project description', 'A new project');
$techStack = $this->ask('Primary tech stack', 'PHP, Laravel');
$currentFocus = $this->ask('Current development focus', 'Initial setup and core features');


$this->createFromStub($path, 'context.md', [
'PROJECT_NAME' => $projectName,
'PROJECT_DESCRIPTION' => $projectDescription,
'TECH_STACK' => $techStack,
'ARCHITECTURE_NOTES' => 'To be documented',
'KEY_COMPONENTS' => 'To be documented',
'CURRENT_FOCUS' => $currentFocus,
'ENVIRONMENT_SETUP' => 'To be documented',
'IMPORTANT_NOTES' => 'To be documented'
]);

$this->createFromStub($path, 'standards.md', [
'PROJECT_NAME' => $projectName,
'CODE_STYLE' => 'Follow PSR-12 standards',
'NAMING_CONVENTIONS' => 'CamelCase for classes, snake_case for variables',
'FILE_ORGANIZATION' => 'Organize by feature/domain',
'DOCUMENTATION_STANDARDS' => 'Use PHPDoc for all public methods',
'TESTING_REQUIREMENTS' => 'Write tests for all new features',
'SECURITY_GUIDELINES' => 'Sanitize all inputs, use prepared statements',
'PERFORMANCE_CONSIDERATIONS' => 'Optimize database queries, cache where appropriate',
'CODE_REVIEW_GUIDELINES' => 'All code must be reviewed before merge'
]);


$this->createFromStub($path, 'workflows/coding.md', [
'PROJECT_NAME' => $projectName,
'DEVELOPMENT_PROCESS' => 'Feature branch workflow with code review',
'BEFORE_STARTING' => 'Check latest main branch, create feature branch',
'IMPLEMENTATION_STEPS' => '1. Write tests 2. Implement feature 3. Run tests 4. Code review',
'TESTING_WORKFLOW' => 'Unit tests, integration tests, manual testing',
'CODE_REVIEW_PROCESS' => 'Pull request review with at least one approval',
'DEPLOYMENT_STEPS' => 'Deploy to staging, test, deploy to production',
'TROUBLESHOOTING' => 'Check logs, reproduce issue, write failing test, fix, verify'
]);

$this->createFromStub($path, 'workflows/planning.md', [
'PROJECT_NAME' => $projectName,
'PLANNING_PROCESS' => 'Requirements gathering, technical design, estimation',
'REQUIREMENTS_GATHERING' => 'Stakeholder interviews, user stories, acceptance criteria',
'TECHNICAL_ANALYSIS' => 'Architecture review, dependency analysis, risk assessment',
'DESIGN_CONSIDERATIONS' => 'User experience, performance, security, maintainability',
'IMPLEMENTATION_PLANNING' => 'Break down into tasks, estimate effort, plan sprints',
'RISK_ASSESSMENT' => 'Identify technical risks, mitigation strategies',
'TIMELINE_ESTIMATION' => 'Story points, velocity tracking, buffer for unknowns'
]);

$this->createFromStub($path, 'workflows/debugging.md', [
'PROJECT_NAME' => $projectName,
'DEBUGGING_PROCESS' => 'Reproduce, isolate, identify root cause, fix, verify',
'COMMON_ISSUES' => 'Database connection, configuration errors, dependency issues',
'DEBUGGING_TOOLS' => 'Debugger, logging, profiler, monitoring tools',
'LOG_ANALYSIS' => 'Check application logs, error logs, system logs',
'PERFORMANCE_DEBUGGING' => 'Profiling, query analysis, resource monitoring',
'ERROR_TRACKING' => 'Use error tracking service, categorize errors, prioritize fixes',
'RESOLUTION_DOCUMENTATION' => 'Document solution, update runbooks, share learnings'
]);


$this->createFromStub($path, 'project/roadmap.md', [
'PROJECT_NAME' => $projectName,
'CURRENT_SPRINT' => 'Project setup and initial development',
'NEXT_SPRINT' => 'Core feature implementation',
'SHORT_TERM_GOALS' => 'MVP development, basic functionality',
'MEDIUM_TERM_GOALS' => 'Feature expansion, performance optimization',
'LONG_TERM_VISION' => 'Full product launch, scaling considerations',
'PRIORITY_FEATURES' => 'User management, core business logic',
'TECHNICAL_DEBT' => 'None identified yet'
]);

$this->createFromStub($path, 'project/decisions.md', [
'PROJECT_NAME' => $projectName,
'RECENT_DECISIONS' => 'Initial technology stack selection',
'KEY_ARCHITECTURE_DECISIONS' => 'Framework choice, database selection, deployment strategy',
'TECHNOLOGY_CHOICES' => $techStack . ' - chosen for team expertise and project requirements',
'DESIGN_PATTERNS' => 'MVC pattern, Repository pattern for data access'
]);

$this->createFromStub($path, 'project/patterns.md', [
'PROJECT_NAME' => $projectName,
'STANDARD_PATTERNS' => 'MVC, Repository, Service Layer patterns',
'COMPONENT_PATTERNS' => 'Reusable components, consistent API structure',
'DATA_HANDLING_PATTERNS' => 'Eloquent models, validation, serialization',
'ERROR_HANDLING_PATTERNS' => 'Custom exceptions, error logging, user-friendly messages',
'TESTING_PATTERNS' => 'Arrange-Act-Assert, test factories, mocking external services',
'CONFIGURATION_PATTERNS' => 'Environment-based config, feature flags',
'PATTERN_EXAMPLES' => 'Service classes for business logic, Resource classes for API responses'
]);


$this->createFromStub($path, 'templates/task.md', [
'TASK_NAME' => '{{TASK_NAME}}',
'TASK_DESCRIPTION' => '{{TASK_DESCRIPTION}}',
'ACCEPTANCE_CRITERIA' => '{{ACCEPTANCE_CRITERIA}}',
'TECHNICAL_REQUIREMENTS' => '{{TECHNICAL_REQUIREMENTS}}',
'IMPLEMENTATION_NOTES' => '{{IMPLEMENTATION_NOTES}}',
'DEPENDENCIES' => '{{DEPENDENCIES}}',
'TESTING_REQUIREMENTS' => '{{TESTING_REQUIREMENTS}}',
'DOCUMENTATION_UPDATES' => '{{DOCUMENTATION_UPDATES}}'
]);

$this->createFromStub($path, 'templates/spec.md', [
'SPEC_NAME' => '{{SPEC_NAME}}',
'SPEC_OVERVIEW' => '{{SPEC_OVERVIEW}}',
'USER_STORIES' => '{{USER_STORIES}}',
'FUNCTIONAL_REQUIREMENTS' => '{{FUNCTIONAL_REQUIREMENTS}}',
'NON_FUNCTIONAL_REQUIREMENTS' => '{{NON_FUNCTIONAL_REQUIREMENTS}}',
'API_SPECIFICATIONS' => '{{API_SPECIFICATIONS}}',
'DATABASE_CHANGES' => '{{DATABASE_CHANGES}}',
'UI_UX_CONSIDERATIONS' => '{{UI_UX_CONSIDERATIONS}}',
'SECURITY_CONSIDERATIONS' => '{{SECURITY_CONSIDERATIONS}}',
'TESTING_STRATEGY' => '{{TESTING_STRATEGY}}',
'IMPLEMENTATION_PLAN' => '{{IMPLEMENTATION_PLAN}}'
]);

$this->info('✅ Zeri project structure initialized successfully!');
$this->line('');
$this->line('Created:');
$this->line('  📁 .zeri/');
$this->line('  📄 .zeri/context.md - Project overview & tech stack');
$this->line('  📄 .zeri/standards.md - Code style & best practices');
$this->line('  📁 .zeri/workflows/ - Development workflows');
$this->line('  📁 .zeri/project/ - Project documentation');
$this->line('  📁 .zeri/specs/ - Feature specifications');
$this->line('  📁 .zeri/templates/ - File templates');
$this->line('');
$this->line('Next steps:');
$this->line('  • Edit .zeri files to match your project');
$this->line('  • Add specifications: zeri add-spec <name>');
$this->line('  • Generate AI files: zeri generate <ai>');

return 0;
}

private function createFromStub(string $basePath, string $relativePath, array $replacements)
{
$stubPath = app_path('../stubs/' . str_replace('.md', '.md.stub', $relativePath));
$targetPath = $basePath . '/.zeri/' . $relativePath;

if (!File::exists($stubPath)) {
$this->error("Stub file not found: {$stubPath}");
return;
}

$content = File::get($stubPath);

foreach ($replacements as $placeholder => $value) {
$content = str_replace('{{' . $placeholder . '}}', $value, $content);
}

File::put($targetPath, $content);
}

public function schedule(Schedule $schedule): void
{

}
}