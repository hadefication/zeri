<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Log;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:log';




protected $description = 'Log: Robust logging services';




private const CONFIG_FILE = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'logging.php';




public function install(): void
{
$this->require('illuminate/log "^12.17"');

$this->task(
'Creating default logging configuration',
function () {
if (! File::exists($this->app->configPath('logging.php'))) {
return File::copy(
static::CONFIG_FILE,
$this->app->configPath('logging.php')
);
}

return false;
}
);

$this->info('Usage:');
$this->comment(
'
use Log;

Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
'
);
}
}
