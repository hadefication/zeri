<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class AddSpecCommand extends Command
{
    protected $signature = 'add-spec {name : Name of the specification} {--path= : Path to .zeri directory} {--force : Force overwrite existing specs and proceed with dirty working directory} {--no-branch : Skip git branch creation}';

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
        $overview = $this->ask('Brief overview of this feature', '');
        if (empty($overview)) {
            $overview = "Brief description of the {$name} feature or enhancement. Explain what this feature does and why it's needed.";
        }

        $replacements = [
            '{{SPEC_NAME}}' => $name,
            '{{SPEC_OVERVIEW}}' => $overview,
            '{{REQUIREMENTS}}' => "- Define the core functionality requirements\n- Specify any user interface requirements\n- List integration or compatibility requirements\n- Note any performance or security requirements",
            '{{IMPLEMENTATION_NOTES}}' => "- Technical considerations and dependencies\n- Files or components that need modification\n- Integration points with existing systems\n- Any architectural decisions or patterns to follow\n- Testing strategy and requirements",
            '{{TODO_ITEMS}}' => '- [ ] Design and plan implementation\n- [ ] Implement core functionality\n- [ ] Add tests\n- [ ] Update documentation\n- [ ] Review and refine\n- [ ] Mark specification as complete',
        ];

        foreach ($replacements as $placeholder => $value) {
            // Convert literal \n to actual newlines
            $processedValue = str_replace('\\n', "\n", $value);
            $content = str_replace($placeholder, $processedValue, $content);
        }

        // Handle git branch creation before creating the file
        $branchCreated = $this->handleGitBranch($specName, $name);
        if ($branchCreated === false) {
            return 1; // Exit if branch creation failed due to uncommitted changes
        }

        File::put($specPath, $content);

        $this->info("✅ Specification '{$name}' created successfully!");
        $this->line("📄 {$specPath}");
        $this->line('');
        $this->line('Edit the specification file to add your requirements and details.');

        return 0;
    }

    private function handleGitBranch($specName, $displayName)
    {
        // Skip if --no-branch flag is provided
        if ($this->option('no-branch')) {
            return true;
        }

        // Check if this is a git repository
        if (! $this->isGitRepository()) {
            return true; // Silently skip for non-git projects
        }

        // Check for dirty working directory
        if ($this->isWorkingDirectoryDirty()) {
            if (! $this->option('force')) {
                $this->warn('Working directory has uncommitted changes.');
                $this->line('Please commit changes before creating a spec branch, or use --force to create a WIP branch with your changes.');

                return false; // Return false to prevent spec file creation
            } else {
                // Create WIP branch and commit changes
                $this->createWipBranch($specName, $displayName);
            }
        }

        // Create and switch to new branch
        $branchName = $this->createBranchName($specName);
        $this->createAndSwitchBranch($branchName, $displayName);

        return true;
    }

    private function isGitRepository()
    {
        return File::exists(getcwd().'/.git') || $this->runGitCommand('git rev-parse --git-dir') !== null;
    }

    private function isWorkingDirectoryDirty()
    {
        $output = $this->runGitCommand('git status --porcelain');

        return ! empty(trim($output));
    }

    private function createWipBranch($specName, $displayName)
    {
        $datetime = $this->getPrettyDatetime();
        $wipBranchName = "wip-{$datetime}";
        $message = "WIP: Auto-commit for {$displayName} spec creation at {$datetime}";

        // Get current branch name
        $currentBranch = trim($this->runGitCommand('git branch --show-current'));

        $this->info("Creating WIP branch '{$wipBranchName}' with uncommitted changes...");

        // Create and switch to WIP branch
        $this->runGitCommand("git checkout -b {$wipBranchName}");

        // Add all changes and commit
        $this->runGitCommand('git add .');
        $this->runGitCommand("git commit -m \"{$message}\"");

        // Switch back to original branch
        if (! empty($currentBranch)) {
            $this->runGitCommand("git checkout {$currentBranch}");
        }

        $this->line("Your changes are saved in branch '{$wipBranchName}'. Switch back with: git checkout {$wipBranchName}");
        $this->line('');
    }

    private function createBranchName($specName)
    {
        $baseBranchName = "feature/{$specName}";

        // Check if branch exists
        if ($this->branchExists($baseBranchName)) {
            // Create pretty datetime suffix
            $datetime = $this->getPrettyDatetime();

            return "{$baseBranchName}-{$datetime}";
        }

        return $baseBranchName;
    }

    private function branchExists($branchName)
    {
        $output = $this->runGitCommand("git show-ref --verify --quiet refs/heads/{$branchName}");

        return $output !== null;
    }

    private function getPrettyDatetime()
    {
        $year = date('Y');
        $month = strtolower(date('M'));
        $day = date('d');
        $hour = date('g');
        $minute = $this->roundMinutes(date('i'));
        $ampm = date('a');

        return "{$year}-{$month}-{$day}-{$hour}{$minute}{$ampm}";
    }

    private function roundMinutes($minutes)
    {
        $min = intval($minutes);
        if ($min <= 14) {
            return '00';
        }
        if ($min <= 29) {
            return '15';
        }
        if ($min <= 44) {
            return '30';
        }

        return '45';
    }

    private function createAndSwitchBranch($branchName, $displayName)
    {
        $this->info("Creating and switching to branch: {$branchName}");

        $output = $this->runGitCommand("git checkout -b {$branchName}");
        if ($output !== null) {
            $this->line("✅ Switched to new branch '{$branchName}'");
        } else {
            $this->warn("Failed to create branch '{$branchName}'");
        }
        $this->line('');
    }

    private function runGitCommand($command)
    {
        $output = [];
        $returnCode = 0;

        exec($command.' 2>/dev/null', $output, $returnCode);

        if ($returnCode === 0) {
            return implode("\n", $output);
        }

        return null;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
