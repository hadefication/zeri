<?php

namespace Illuminate\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;

class PackageManifest
{





public $files;






public $basePath;






public $vendorPath;






public $manifestPath;






public $manifest;








public function __construct(Filesystem $files, $basePath, $manifestPath)
{
$this->files = $files;
$this->basePath = $basePath;
$this->manifestPath = $manifestPath;
$this->vendorPath = Env::get('COMPOSER_VENDOR_DIR') ?: $basePath.'/vendor';
}






public function providers()
{
return $this->config('providers');
}






public function aliases()
{
return $this->config('aliases');
}







public function config($key)
{
return (new Collection($this->getManifest()))
->flatMap(fn ($configuration) => (array) ($configuration[$key] ?? []))
->filter()
->all();
}






protected function getManifest()
{
if (! is_null($this->manifest)) {
return $this->manifest;
}

if (! is_file($this->manifestPath)) {
$this->build();
}

return $this->manifest = is_file($this->manifestPath) ?
$this->files->getRequire($this->manifestPath) : [];
}






public function build()
{
$packages = [];

if ($this->files->exists($path = $this->vendorPath.'/composer/installed.json')) {
$installed = json_decode($this->files->get($path), true);

$packages = $installed['packages'] ?? $installed;
}

$ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

$this->write((new Collection($packages))->mapWithKeys(function ($package) {
return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
})->each(function ($configuration) use (&$ignore) {
$ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
})->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
return $ignoreAll || in_array($package, $ignore);
})->filter()->all());
}







protected function format($package)
{
return str_replace($this->vendorPath.'/', '', $package);
}






protected function packagesToIgnore()
{
if (! is_file($this->basePath.'/composer.json')) {
return [];
}

return json_decode(file_get_contents(
$this->basePath.'/composer.json'
), true)['extra']['laravel']['dont-discover'] ?? [];
}









protected function write(array $manifest)
{
if (! is_writable($dirname = dirname($this->manifestPath))) {
throw new Exception("The {$dirname} directory must be present and writable.");
}

$this->files->replace(
$this->manifestPath, '<?php return '.var_export($manifest, true).';'
);
}
}
