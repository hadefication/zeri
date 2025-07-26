<?php

namespace Illuminate\Cache\Console;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'cache:clear')]
class ClearCommand extends Command
{





protected $name = 'cache:clear';






protected $description = 'Flush the application cache';






protected $cache;






protected $files;







public function __construct(CacheManager $cache, Filesystem $files)
{
parent::__construct();

$this->cache = $cache;
$this->files = $files;
}






public function handle()
{
$this->laravel['events']->dispatch(
'cache:clearing', [$this->argument('store'), $this->tags()]
);

$successful = $this->cache()->flush();

$this->flushFacades();

if (! $successful) {
return $this->components->error('Failed to clear cache. Make sure you have the appropriate permissions.');
}

$this->laravel['events']->dispatch(
'cache:cleared', [$this->argument('store'), $this->tags()]
);

$this->components->info('Application cache cleared successfully.');
}






public function flushFacades()
{
if (! $this->files->exists($storagePath = storage_path('framework/cache'))) {
return;
}

foreach ($this->files->files($storagePath) as $file) {
if (preg_match('/facade-.*\.php$/', $file)) {
$this->files->delete($file);
}
}
}






protected function cache()
{
$cache = $this->cache->store($this->argument('store'));

return empty($this->tags()) ? $cache : $cache->tags($this->tags());
}






protected function tags()
{
return array_filter(explode(',', $this->option('tags') ?? ''));
}






protected function getArguments()
{
return [
['store', InputArgument::OPTIONAL, 'The name of the store you would like to clear'],
];
}






protected function getOptions()
{
return [
['tags', null, InputOption::VALUE_OPTIONAL, 'The cache tags you would like to clear', null],
];
}
}
