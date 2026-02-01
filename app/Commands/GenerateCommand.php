<?php

namespace App\Commands;

use App\Generators\ClaudeGenerator;
use App\Generators\CodexGenerator;
use App\Generators\CursorGenerator;
use App\Generators\GeminiGenerator;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class GenerateCommand extends Command
{
    protected $signature = 'generate
                            {ai? : AI type (claude, gemini, cursor, codex, all)}
                            {--all : Generate for all AI types}
                            {--path= : Path to project directory}
                            {--force : Force regeneration even if files are up to date}
                            {--backup : Create backup of existing files before overwriting}
                            {--interactive : Ask before overwriting files with manual changes}
                            {--position=prepend : Position to inject reference (prepend or append)}';

    protected $description = 'Generate AI-specific instruction files';

    private array $validAIs = ['claude', 'gemini', 'cursor', 'codex', 'all'];

    private array $validPositions = ['prepend', 'append'];

    public function handle()
    {
        $ai = $this->argument('ai');
        $allFlag = $this->option('all');
        $path = $this->option('path') ?: getcwd();
        $force = $this->option('force');
        $backup = $this->option('backup');
        $interactive = $this->option('interactive');
        $position = $this->option('position') ?: 'prepend';

        // Validate position
        if (! in_array($position, $this->validPositions)) {
            $this->error('Invalid position. Valid options: '.implode(', ', $this->validPositions));

            return 1;
        }

        // Handle --all flag or missing ai argument
        if ($allFlag || ! $ai) {
            if ($allFlag) {
                $ai = 'all';
            } elseif (! $ai) {
                $this->error('Please specify an AI type or use --all flag. Valid options: '.implode(', ', array_slice($this->validAIs, 0, -1)).', or --all');

                return 1;
            }
        }

        $ai = strtolower($ai);
        $zeriPath = $path.'/.zeri';

        if (! File::exists($zeriPath)) {
            $this->error('.zeri directory not found. Run "zeri init" first.');

            return 1;
        }

        if (! in_array($ai, $this->validAIs)) {
            $this->error('Invalid AI type. Valid options: '.implode(', ', $this->validAIs));

            return 1;
        }

        // Check for old structure
        if ($this->hasOldStructure($zeriPath)) {
            $this->error('Old zeri structure detected (project.md + development.md)');
            $this->line('');
            $this->line("Run 'zeri migrate' to upgrade to the new consolidated format.");
            $this->line("Optionally use 'zeri migrate --backup' to keep copies of old files.");

            return 1;
        }

        // Check for new structure
        if (! $this->hasNewStructure($zeriPath)) {
            $this->error('.zeri/ZERI.md not found.');
            $this->line('');
            $this->line("Run 'zeri init' to initialize the project structure.");

            return 1;
        }

        $generators = $this->getGenerators($ai, $zeriPath, $path, $position);
        $generated = [];
        $skipped = [];

        foreach ($generators as $name => $generator) {
            $this->line("Processing {$name} file...");

            try {
                $wasGenerated = $generator->generate($force, $backup, $interactive);

                if ($wasGenerated) {
                    $files = $generator->getGeneratedFiles();
                    foreach ($files as $filename) {
                        $generated[] = $filename;
                    }
                    $primaryFile = $generator->getOutputFileName();
                    $this->info("✅ Updated: {$primaryFile}");
                } else {
                    $files = $generator->getGeneratedFiles();
                    foreach ($files as $filename) {
                        $skipped[] = $filename;
                    }
                    $primaryFile = $generator->getOutputFileName();
                    $this->line("⏭️  Skipped: {$primaryFile} (reference already exists)");
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to process {$name}: ".$e->getMessage());

                return 1;
            }
        }

        // Summary
        $this->line('');
        if (! empty($generated)) {
            $this->info('Updated files:');
            foreach ($generated as $file) {
                $this->line("  📄 {$file}");
            }
        }

        if (! empty($skipped)) {
            $this->line('Skipped files (reference already present):');
            foreach ($skipped as $file) {
                $this->line("  📄 {$file}");
            }
        }

        if (empty($generated) && empty($skipped)) {
            $this->line('No files to process.');
        }

        $this->line('');
        $this->line('💡 AI files now reference .zeri/ZERI.md for project context');

        return 0;
    }

    private function hasOldStructure(string $zeriPath): bool
    {
        $hasProjectMd = File::exists($zeriPath.'/project.md');
        $hasDevelopmentMd = File::exists($zeriPath.'/development.md');
        $hasZeriMd = File::exists($zeriPath.'/ZERI.md');

        return ($hasProjectMd || $hasDevelopmentMd) && ! $hasZeriMd;
    }

    private function hasNewStructure(string $zeriPath): bool
    {
        return File::exists($zeriPath.'/ZERI.md');
    }

    private function getGenerators(string $ai, string $zeriPath, string $outputPath, string $position): array
    {
        $generators = [];

        if ($ai === 'all') {
            $generators['Claude'] = new ClaudeGenerator($zeriPath, $outputPath, $position);
            $generators['Gemini'] = new GeminiGenerator($zeriPath, $outputPath, $position);
            $generators['Cursor'] = new CursorGenerator($zeriPath, $outputPath, $position);
            $generators['Codex'] = new CodexGenerator($zeriPath, $outputPath, $position);
        } else {
            switch ($ai) {
                case 'claude':
                    $generators['Claude'] = new ClaudeGenerator($zeriPath, $outputPath, $position);
                    break;
                case 'gemini':
                    $generators['Gemini'] = new GeminiGenerator($zeriPath, $outputPath, $position);
                    break;
                case 'cursor':
                    $generators['Cursor'] = new CursorGenerator($zeriPath, $outputPath, $position);
                    break;
                case 'codex':
                    $generators['Codex'] = new CodexGenerator($zeriPath, $outputPath, $position);
                    break;
            }
        }

        return $generators;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
