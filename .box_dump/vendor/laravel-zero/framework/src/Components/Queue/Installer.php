<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Queue;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:queue';




protected $description = 'Queues: Unified API across a variety of queue services';




private const CONFIG_FILE = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'queue.php';




public function install(): void
{
$this->call('app:install', ['component' => 'database']);

$this->require('illuminate/bus "^12.17"');
$this->require('illuminate/queue "^12.17"');

$this->task(
'Creating default queue configuration',
function () {
if (! File::exists($this->app->configPath('queue.php'))) {
return File::copy(
self::CONFIG_FILE,
$this->app->configPath('queue.php')
);
}

return false;
}
);
}
}
