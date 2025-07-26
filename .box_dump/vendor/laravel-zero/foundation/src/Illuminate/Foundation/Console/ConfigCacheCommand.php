<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Filesystem\Filesystem;
use LogicException;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'config:cache')]
class ConfigCacheCommand extends Command
{





protected $name = 'config:cache';






protected $description = 'Create a cache file for faster configuration loading';






protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}








public function handle()
{
$this->callSilent('config:clear');

$config = $this->getFreshConfiguration();

$configPath = $this->laravel->getCachedConfigPath();

$this->files->put(
$configPath, '<?php return '.var_export($config, true).';'.PHP_EOL
);

try {
require $configPath;
} catch (Throwable $e) {
$this->files->delete($configPath);

throw new LogicException('Your configuration files are not serializable.', 0, $e);
}

$this->components->info('Configuration cached successfully.');
}






protected function getFreshConfiguration()
{
$app = require $this->laravel->bootstrapPath('app.php');

$app->useStoragePath($this->laravel->storagePath());

$app->make(ConsoleKernelContract::class)->bootstrap();

return $app['config']->all();
}
}
