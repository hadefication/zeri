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
                            {ai? : AI type (claude, gemini, cursor, all)} 
                            {--all : Generate for all AI types}
                            {--path= : Path to project directory}
                            {--force : Force regeneration even if files are up to date}
                            {--backup : Create backup of existing files before overwriting}
                            {--interactive : Ask before overwriting files with manual changes}';

    protected $description = 'Generate AI-specific instruction files';

    private array $validAIs = ['claude', 'gemini', 'cursor', 'codex', 'all'];

    public function handle()
    {
        $ai = $this->argument('ai');
        $allFlag = $this->option('all');
        $path = $this->option('path') ?: getcwd();
        $force = $this->option('force');
        $backup = $this->option('backup');
        $interactive = $this->option('interactive');

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

        $generators = $this->getGenerators($ai, $zeriPath, $path);
        $generated = [];
        $skipped = [];

        foreach ($generators as $name => $generator) {
            $this->line("Generating {$name} file...");

            try {
                $wasGenerated = $generator->generate($force, $backup, $interactive);

                if ($wasGenerated) {
                    $files = $generator->getGeneratedFiles();
                    foreach ($files as $filename) {
                        $generated[] = $filename;
                    }
                    $primaryFile = $generator->getOutputFileName();
                    $this->info("✅ Generated: {$primaryFile}");
                } else {
                    $files = $generator->getGeneratedFiles();
                    foreach ($files as $filename) {
                        $skipped[] = $filename;
                    }
                    $primaryFile = $generator->getOutputFileName();
                    $this->line("⏭️  Skipped: {$primaryFile} (up to date)");
                }
            } catch (\Exception $e) {
                $this->error("❌ Failed to generate {$name}: ".$e->getMessage());

                return 1;
            }
        }

        // Summary
        $this->line('');
        if (! empty($generated)) {
            $this->info('Generated files:');
            foreach ($generated as $file) {
                $this->line("  📄 {$file}");
            }
        }

        if (! empty($skipped)) {
            $this->line('Skipped files (use --force to regenerate):');
            foreach ($skipped as $file) {
                $this->line("  📄 {$file}");
            }
        }

        if (empty($generated) && empty($skipped)) {
            $this->line('No files to generate.');
        }

        $this->line('');
        $this->line('💡 Tip: Use --force to regenerate files even when up to date');

        return 0;
    }

    private function getGenerators(string $ai, string $zeriPath, string $outputPath): array
    {
        $generators = [];

        if ($ai === 'all') {
            $generators['Claude'] = new ClaudeGenerator($zeriPath, $outputPath);
            $generators['Gemini'] = new GeminiGenerator($zeriPath, $outputPath);
            $generators['Cursor'] = new CursorGenerator($zeriPath, $outputPath);
            $generators['Codex'] = new CodexGenerator($zeriPath, $outputPath);

        } else {
            switch ($ai) {
                case 'claude':
                    $generators['Claude'] = new ClaudeGenerator($zeriPath, $outputPath);
                    break;
                case 'gemini':
                    $generators['Gemini'] = new GeminiGenerator($zeriPath, $outputPath);
                    break;
                case 'cursor':
                    $generators['Cursor'] = new CursorGenerator($zeriPath, $outputPath);
                    break;
                case 'codex':
                    $generators['Codex'] = new CodexGenerator($zeriPath, $outputPath);
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
