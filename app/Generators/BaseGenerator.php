<?php

namespace App\Generators;

use Illuminate\Support\Facades\File;

abstract class BaseGenerator
{
    protected string $zeriPath;
    protected string $outputPath;

    public function __construct(string $zeriPath, string $outputPath)
    {
        $this->zeriPath = $zeriPath;
        $this->outputPath = $outputPath;
    }

    abstract public function generate(bool $force = false): bool;

    abstract public function getOutputFileName(): string;

    public function getGeneratedFiles(): array
    {
        // Default implementation returns single output file
        return [$this->getOutputFileName()];
    }

    protected function shouldRegenerate(bool $force): bool
    {
        if ($force) {
            return true;
        }

        $outputFile = $this->outputPath . '/' . $this->getOutputFileName();
        
        if (!File::exists($outputFile)) {
            return true;
        }

        $outputTime = File::lastModified($outputFile);
        
        // Check if any .zeri files are newer than the output file
        $zeriFiles = $this->getZeriFiles();
        
        foreach ($zeriFiles as $file) {
            if (File::exists($file) && File::lastModified($file) > $outputTime) {
                return true;
            }
        }

        return false;
    }

    protected function getZeriFiles(): array
    {
        $files = [];
        
        // Core files
        $coreFiles = [
            'context.md',
            'standards.md'
        ];
        
        foreach ($coreFiles as $file) {
            $files[] = $this->zeriPath . '/' . $file;
        }
        
        // Workflow files
        $workflowDir = $this->zeriPath . '/workflows';
        if (File::exists($workflowDir)) {
            $workflowFiles = File::files($workflowDir);
            foreach ($workflowFiles as $file) {
                $files[] = $file->getPathname();
            }
        }
        
        // Project files
        $projectDir = $this->zeriPath . '/project';
        if (File::exists($projectDir)) {
            $projectFiles = File::files($projectDir);
            foreach ($projectFiles as $file) {
                $files[] = $file->getPathname();
            }
        }
        
        // Specification files
        $specsDir = $this->zeriPath . '/specs';
        if (File::exists($specsDir)) {
            $specFiles = File::files($specsDir);
            foreach ($specFiles as $file) {
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }

    protected function readFile(string $relativePath): string
    {
        $fullPath = $this->zeriPath . '/' . $relativePath;
        return File::exists($fullPath) ? File::get($fullPath) : '';
    }

    protected function getSpecifications(): array
    {
        $specsDir = $this->zeriPath . '/specs';
        $specs = [];
        
        if (File::exists($specsDir)) {
            $specFiles = File::files($specsDir);
            foreach ($specFiles as $file) {
                $specs[] = [
                    'name' => pathinfo($file->getFilename(), PATHINFO_FILENAME),
                    'content' => File::get($file->getPathname())
                ];
            }
        }
        
        return $specs;
    }

    protected function writeOutput(string $content): bool
    {
        $outputFile = $this->outputPath . '/' . $this->getOutputFileName();
        
        // Ensure the directory exists
        $directory = dirname($outputFile);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        return File::put($outputFile, $content) !== false;
    }

    protected function writeFile(string $filename, string $content): bool
    {
        $outputFile = $this->outputPath . '/' . $filename;
        
        // Ensure the directory exists
        $directory = dirname($outputFile);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        
        return File::put($outputFile, $content) !== false;
    }

    protected function createFromStub(string $stubName, array $replacements): string
    {
        $stubPath = app_path('../stubs/' . $stubName);
        
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }

        $content = File::get($stubPath);
        
        foreach ($replacements as $placeholder => $value) {
            // Convert literal \n to actual newlines
            $processedValue = str_replace('\\n', "\n", $value);
            $content = str_replace('{{' . $placeholder . '}}', $processedValue, $content);
        }

        return $content;
    }
}