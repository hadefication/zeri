<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\View;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Components\AbstractInstaller;




final class Installer extends AbstractInstaller
{



protected $name = 'install:view';




protected $description = 'View: Blade View Components';




private const CONFIG_FILE = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'view.php';




public function install(): void
{
$this->require('illuminate/view "^12.17"');

$this->task(
'Creating resources/views folder',
function () {
if (! File::exists($this->app->resourcePath('views'))) {
File::makeDirectory($this->app->resourcePath('views'), 0755, true, true);

return true;
}

return false;
}
);

$this->task(
'Creating default view configuration',
function () {
if (! File::exists($this->app->configPath('view.php'))) {
return File::copy(
static::CONFIG_FILE,
$this->app->configPath('view.php')
);
}

return false;
}
);

$this->task(
'Creating cache storage folder',
function () {
if (File::exists($this->app->storagePath('app/.gitignore')) &&
File::exists($this->app->storagePath('framework/views/.gitignore'))
) {
return false;
}

if (! File::exists($this->app->storagePath('app'))) {
File::makeDirectory($this->app->storagePath('app'), 0755, true, true);
}

if (! File::exists($this->app->storagePath('app/.gitignore'))) {
File::append($this->app->storagePath('app/.gitignore'), "*\n!.gitignore");
}

if (! File::exists($this->app->storagePath('framework/views'))) {
File::makeDirectory($this->app->storagePath('framework/views'), 0755, true, true);
}

if (! File::exists($this->app->storagePath('framework/views/.gitignore'))) {
File::append($this->app->storagePath('framework/views/.gitignore'), "*\n!.gitignore");
}

return true;
}
);
}
}
