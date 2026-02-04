<?php

namespace App\Generators;

use Illuminate\Support\Facades\File;

abstract class BaseGenerator
{
    protected string $zeriPath;

    protected string $outputPath;

    protected string $position = 'prepend';

    public const ZERI_REFERENCE = <<<'REFERENCE'
<!-- IMPORTANT: Read this file first -->
# Zeri Project Context

**IMPORTANT:** Before proceeding, you MUST read the project context file:

→ **[@.zeri/ZERI.md](.zeri/ZERI.md)** ← Contains all project instructions, specifications, and development guidelines.

This file is the single source of truth for AI assistants working on this project.
REFERENCE;

    public function __construct(string $zeriPath, string $outputPath, string $position = 'prepend')
    {
        $this->zeriPath = $zeriPath;
        $this->outputPath = $outputPath;
        $this->position = $position;
    }

    abstract public function generate(bool $replace = false): bool;

    abstract public function getOutputFileName(): string;

    public function getGeneratedFiles(): array
    {
        // Default implementation returns single output file
        return [$this->getOutputFileName()];
    }

    protected function getZeriFiles(): array
    {
        $files = [];

        // Main ZERI.md file
        $files[] = $this->zeriPath.'/ZERI.md';

        // Specification files
        $specsDir = $this->zeriPath.'/specs';
        if (File::exists($specsDir)) {
            $specFiles = File::files($specsDir);
            foreach ($specFiles as $file) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Check if the .zeri directory has the old structure (project.md + development.md)
     */
    public function hasOldStructure(): bool
    {
        $hasProjectMd = File::exists($this->zeriPath.'/project.md');
        $hasDevelopmentMd = File::exists($this->zeriPath.'/development.md');
        $hasZeriMd = File::exists($this->zeriPath.'/ZERI.md');

        // Old structure if project.md or development.md exists and ZERI.md doesn't
        return ($hasProjectMd || $hasDevelopmentMd) && ! $hasZeriMd;
    }

    /**
     * Check if the .zeri directory has the new structure (ZERI.md)
     */
    public function hasNewStructure(): bool
    {
        return File::exists($this->zeriPath.'/ZERI.md');
    }

    /**
     * Inject the ZERI.md reference into an AI file
     */
    protected function injectReference(string $filePath, bool $replace = false): bool
    {
        $reference = self::ZERI_REFERENCE;

        // Ensure the directory exists
        $directory = dirname($filePath);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Replace mode: overwrite completely with just the reference
        if ($replace) {
            File::put($filePath, $reference."\n");

            return true;
        }

        // Default mode: inject reference if not already present
        if (File::exists($filePath)) {
            $content = File::get($filePath);

            // Check if reference already exists (exact match)
            if (str_contains($content, $reference)) {
                return false; // No change needed
            }

            // Inject reference based on position
            if ($this->position === 'prepend') {
                $content = $reference."\n\n".$content;
            } else {
                $content = $content."\n\n".$reference;
            }

            File::put($filePath, $content);

            return true;
        } else {
            // Create minimal file with just reference
            File::put($filePath, $reference."\n");

            return true;
        }
    }

    protected function readFile(string $relativePath): string
    {
        $fullPath = $this->zeriPath.'/'.$relativePath;

        return File::exists($fullPath) ? File::get($fullPath) : '';
    }

    protected function getSpecifications(): array
    {
        $specsDir = $this->zeriPath.'/specs';
        $specs = [];

        if (File::exists($specsDir)) {
            $specFiles = File::files($specsDir);
            foreach ($specFiles as $file) {
                $specs[] = [
                    'name' => pathinfo($file->getFilename(), PATHINFO_FILENAME),
                    'content' => File::get($file->getPathname()),
                ];
            }
        }

        return $specs;
    }

    protected function writeOutput(string $content): bool
    {
        $outputFile = $this->outputPath.'/'.$this->getOutputFileName();

        // Ensure the directory exists
        $directory = dirname($outputFile);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return File::put($outputFile, $content) !== false;
    }

    protected function writeFile(string $filename, string $content): bool
    {
        $outputFile = $this->outputPath.'/'.$filename;

        // Ensure the directory exists
        $directory = dirname($outputFile);
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return File::put($outputFile, $content) !== false;
    }

    protected function createFromStub(string $stubName, array $replacements): string
    {
        $stubPath = app_path('../stubs/'.$stubName);

        if (! File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }

        $content = File::get($stubPath);

        foreach ($replacements as $placeholder => $value) {
            // Convert literal \n to actual newlines
            $processedValue = str_replace('\\n', "\n", $value);
            $content = str_replace('{{'.$placeholder.'}}', $processedValue, $content);
        }

        return $content;
    }
}
