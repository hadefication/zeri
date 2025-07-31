<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class AddSpecCommand extends Command
{
    protected $signature = 'add-spec {name : Name of the specification} {--path= : Path to .zeri directory} {--force : Force overwrite if specification already exists}';

    protected $description = 'Create a new specification file';

    public function handle()
    {
        $name = $this->argument('name');
        $path = $this->option('path') ?: getcwd();
        $force = $this->option('force');
        $zeriPath = $path.'/.zeri';

        if (! File::exists($zeriPath)) {
            $this->error('.zeri directory not found. Run "zeri init" first.');

            return 1;
        }

        $specName = str_replace(' ', '-', strtolower($name));
        $specPath = $zeriPath.'/specs/'.$specName.'.md';

        if (File::exists($specPath) && ! $force) {
            $this->error("Specification '{$specName}' already exists!");
            $this->line('Use --force to overwrite the existing specification.');

            return 1;
        }

        if (File::exists($specPath) && $force) {
            $this->warn("⚠️  Specification '{$specName}' already exists!");
            $this->line('');
            if (! $this->confirm('Do you want to overwrite the existing specification?', false)) {
                $this->info('Operation cancelled.');

                return 0;
            }
            $this->line('');
        }

        // Get template content
        $templatePath = $zeriPath.'/templates/spec.md';
        if (! File::exists($templatePath)) {
            $this->error('Specification template not found. Please ensure .zeri is properly initialized.');

            return 1;
        }

        $content = File::get($templatePath);

        // Replace placeholders with actual values
        $replacements = [
            '{{SPEC_NAME}}' => $name,
            '{{SPEC_OVERVIEW}}' => $this->ask('Brief overview of this feature', 'Feature description'),
            '{{REQUIREMENTS}}' => '- Requirement 1\n- Requirement 2\n- Requirement 3',
            '{{IMPLEMENTATION_NOTES}}' => 'Any technical considerations, dependencies, or important implementation details.',
            '{{TODO_ITEMS}}' => '- [ ] Design and plan implementation\n- [ ] Implement core functionality\n- [ ] Add tests\n- [ ] Update documentation\n- [ ] Review and refine\n- [ ] Mark specification as complete',
        ];

        foreach ($replacements as $placeholder => $value) {
            // Convert literal \n to actual newlines
            $processedValue = str_replace('\\n', "\n", $value);
            $content = str_replace($placeholder, $processedValue, $content);
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
        // $schedule->command(static::class)->everyMinute();
    }
}
