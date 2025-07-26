<?php

namespace App\Commands;

use App\Generators\ClaudeGenerator;
use App\Generators\CursorGenerator;
use App\Generators\GeminiGenerator;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class GenerateCommand extends Command
{
protected $signature = 'generate 
                            {ai : AI type (claude, gemini, cursor, all)} 
                            {--path= : Path to project directory}
                            {--force : Force regeneration even if files are up to date}
                            {--open : Open generated files after creation}';

protected $description = 'Generate AI-specific instruction files';

private array $validAIs = ['claude', 'gemini', 'cursor', 'all'];

public function handle()
{
$ai = strtolower($this->argument('ai'));
$path = $this->option('path') ?: getcwd();
$force = $this->option('force');
$open = $this->option('open');

$zeriPath = $path . '/.zeri';

if (!File::exists($zeriPath)) {
$this->error('.zeri directory not found. Run "zeri init" first.');
return 1;
}

if (!in_array($ai, $this->validAIs)) {
$this->error("Invalid AI type. Valid options: " . implode(', ', $this->validAIs));
return 1;
}

$generators = $this->getGenerators($ai, $zeriPath, $path);
$generated = [];
$skipped = [];

foreach ($generators as $name => $generator) {
$this->line("Generating {$name} file...");

try {
$wasGenerated = $generator->generate($force);

if ($wasGenerated) {
$filename = $generator->getOutputFileName();
$generated[] = $filename;
$this->info("✅ Generated: {$filename}");
} else {
$filename = $generator->getOutputFileName();
$skipped[] = $filename;
$this->line("⏭️  Skipped: {$filename} (up to date)");
}
} catch (\Exception $e) {
$this->error("❌ Failed to generate {$name}: " . $e->getMessage());
return 1;
}
}


$this->line('');
if (!empty($generated)) {
$this->info('Generated files:');
foreach ($generated as $file) {
$this->line("  📄 {$file}");
}
}

if (!empty($skipped)) {
$this->line('Skipped files (use --force to regenerate):');
foreach ($skipped as $file) {
$this->line("  📄 {$file}");
}
}

if (empty($generated) && empty($skipped)) {
$this->line('No files to generate.');
}


if ($open && !empty($generated)) {
$this->openFiles($generated, $path);
}

$this->line('');
$this->line('💡 Tip: Use --force to regenerate files even when up to date');
$this->line('💡 Tip: Use --open to automatically open generated files');

return 0;
}

private function getGenerators(string $ai, string $zeriPath, string $outputPath): array
{
$generators = [];

if ($ai === 'all') {
$generators['Claude'] = new ClaudeGenerator($zeriPath, $outputPath);
$generators['Gemini'] = new GeminiGenerator($zeriPath, $outputPath);
$generators['Cursor'] = new CursorGenerator($zeriPath, $outputPath);
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
}
}

return $generators;
}

private function openFiles(array $files, string $path): void
{
$this->line('');
$this->line('Opening generated files...');

foreach ($files as $file) {
$fullPath = $path . '/' . $file;

if (File::exists($fullPath)) {

$command = $this->getOpenCommand($fullPath);

if ($command) {
exec($command . ' 2>/dev/null &');
$this->line("📖 Opened: {$file}");
} else {
$this->line("⚠️  Could not open: {$file} (no suitable application found)");
}
}
}
}

private function getOpenCommand(string $filePath): ?string
{
$escapedPath = escapeshellarg($filePath);


if (PHP_OS_FAMILY === 'Darwin') {
return "open {$escapedPath}";
} elseif (PHP_OS_FAMILY === 'Windows') {
return "start {$escapedPath}";
} elseif (PHP_OS_FAMILY === 'Linux') {
return "xdg-open {$escapedPath}";
}

return null;
}

public function schedule(Schedule $schedule): void
{

}
}