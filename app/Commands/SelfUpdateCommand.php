<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class SelfUpdateCommand extends Command
{
    protected $signature = 'self-update {--check : Check for updates without downloading} {--force : Force update even if on latest version}';
    protected $description = 'Update Zeri to the latest version';

    public function handle(): int
    {
        if ($this->option('check')) {
            return $this->checkForUpdates();
        }

        $this->info('🚀 Starting self-update process...');
        
        // Use the update script that handles all the logic
        $scriptUrl = 'https://raw.githubusercontent.com/hadefication/zeri/main/scripts/update.sh';
        
        $command = "curl -sSL {$scriptUrl} | bash";
        
        if ($this->option('force')) {
            $command .= ' -s -- --force';
        }
        
        $this->line('Executing update script...');
        
        // Execute the update script
        $result = null;
        $output = [];
        exec($command . ' 2>&1', $output, $result);
        
        // Display the output from the script
        foreach ($output as $line) {
            $this->line($line);
        }
        
        if ($result === 0) {
            $this->line('');
            $this->info('🎉 Self-update completed successfully!');
            return 0;
        } else {
            $this->line('');
            $this->error('Self-update failed. You can manually update by downloading the latest release:');
            $this->line('https://github.com/hadefication/zeri/releases/latest');
            return 1;
        }
    }

    private function checkForUpdates(): int
    {
        $this->info('🔍 Checking for updates...');
        
        try {
            // Get current version
            $currentVersion = $this->getApplication()->getVersion();
            
            // Get latest version from GitHub API using curl
            $output = [];
            $result = null;
            exec('curl -s https://api.github.com/repos/hadefication/zeri/releases/latest 2>/dev/null', $output, $result);
            
            if ($result !== 0 || empty($output)) {
                $this->error('Failed to check for updates. Please check your internet connection.');
                return 1;
            }
            
            $response = implode("\n", $output);
            
            $data = json_decode($response, true);
            if (!isset($data['tag_name'])) {
                $this->error('Could not parse release information from GitHub.');
                return 1;
            }
            
            $latestVersion = ltrim($data['tag_name'], 'v');
            
            $this->line('Current version: ' . $currentVersion);
            $this->line('Latest version: ' . $latestVersion);
            
            if (version_compare($currentVersion, $latestVersion, '<')) {
                $this->info('🎉 An update is available!');
                $this->line('Run "zeri self-update" to update to the latest version.');
                return 0;
            } else {
                $this->info('✅ You are running the latest version.');
                return 0;
            }
        } catch (\Exception $e) {
            $this->error('Error checking for updates: ' . $e->getMessage());
            return 1;
        }
    }
}