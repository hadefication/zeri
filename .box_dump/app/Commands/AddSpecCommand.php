<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class AddSpecCommand extends Command
{
protected $signature = 'add-spec {name : Name of the specification} {--path= : Path to .zeri directory}';

protected $description = 'Create a new specification file';

public function handle()
{
$name = $this->argument('name');
$path = $this->option('path') ?: getcwd();
$zeriPath = $path . '/.zeri';

if (!File::exists($zeriPath)) {
$this->error('.zeri directory not found. Run "zeri init" first.');
return 1;
}

$specName = str_replace(' ', '-', strtolower($name));
$specPath = $zeriPath . '/specs/' . $specName . '.md';

if (File::exists($specPath)) {
$this->error("Specification '{$specName}' already exists!");
return 1;
}


$templatePath = $zeriPath . '/templates/spec.md';
if (!File::exists($templatePath)) {
$this->error('Specification template not found. Please ensure .zeri is properly initialized.');
return 1;
}

$content = File::get($templatePath);


$replacements = [
'{{SPEC_NAME}}' => $name,
'{{SPEC_OVERVIEW}}' => $this->ask('Brief overview of this feature', 'Feature description'),
'{{USER_STORIES}}' => 'As a user, I want...',
'{{FUNCTIONAL_REQUIREMENTS}}' => '- Requirement 1\n- Requirement 2',
'{{NON_FUNCTIONAL_REQUIREMENTS}}' => '- Performance: < 200ms response time\n- Security: Authentication required',
'{{API_SPECIFICATIONS}}' => 'API endpoints and data structures',
'{{DATABASE_CHANGES}}' => 'New tables, columns, or modifications needed',
'{{UI_UX_CONSIDERATIONS}}' => 'User interface and experience requirements',
'{{SECURITY_CONSIDERATIONS}}' => 'Authentication, authorization, data protection',
'{{TESTING_STRATEGY}}' => 'Unit tests, integration tests, acceptance criteria',
'{{IMPLEMENTATION_PLAN}}' => 'Phase 1: ...\nPhase 2: ...'
];

foreach ($replacements as $placeholder => $value) {
$content = str_replace($placeholder, $value, $content);
}

File::put($specPath, $content);

$this->info("✅ Specification '{$name}' created successfully!");
$this->line("📄 {$specPath}");
$this->line('');
$this->line('Edit the specification file to add your requirements and details.');

return 0;
}

public function schedule(Schedule $schedule): void
{

}
}