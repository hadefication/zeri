<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Providers\Filesystem;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemServiceProvider as BaseServiceProvider;




final class FilesystemServiceProvider extends BaseServiceProvider
{



public function register(): void
{
parent::register();

$this->app->alias('filesystem.disk', Filesystem::class);

$config = $this->app->make('config');

if ($config->get('filesystems.default') === null) {
$config->set('filesystems', $this->getDefaultConfig());
}
}






protected function getDefaultConfig(): array
{
return [
'default' => 'local',
'disks' => [
'local' => [
'driver' => 'local',
'root' => $this->app->storagePath('app'),
],
],
];
}


protected function shouldServeFiles(array $config)
{
return false;
}
}
