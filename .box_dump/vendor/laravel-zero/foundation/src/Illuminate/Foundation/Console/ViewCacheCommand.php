<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

#[AsCommand(name: 'view:cache')]
class ViewCacheCommand extends Command
{





protected $signature = 'view:cache';






protected $description = "Compile all of the application's Blade templates";






public function handle()
{
$this->callSilent('view:clear');

$this->paths()->each(function ($path) {
$prefix = $this->output->isVeryVerbose() ? '<fg=yellow;options=bold>DIR</> ' : '';

$this->components->task($prefix.$path, null, OutputInterface::VERBOSITY_VERBOSE);

$this->compileViews($this->bladeFilesIn([$path]));
});

$this->newLine();

$this->components->info('Blade templates cached successfully.');
}







protected function compileViews(Collection $views)
{
$compiler = $this->laravel['view']->getEngineResolver()->resolve('blade')->getCompiler();

$views->map(function (SplFileInfo $file) use ($compiler) {
$this->components->task('    '.$file->getRelativePathname(), null, OutputInterface::VERBOSITY_VERY_VERBOSE);

$compiler->compile($file->getRealPath());
});

if ($this->output->isVeryVerbose()) {
$this->newLine();
}
}







protected function bladeFilesIn(array $paths)
{
$extensions = (new Collection($this->laravel['view']->getExtensions()))
->filter(fn ($value) => $value === 'blade')
->keys()
->map(fn ($extension) => "*.{$extension}")
->all();

return new Collection(
Finder::create()
->in($paths)
->exclude('vendor')
->name($extensions)
->files()
);
}






protected function paths()
{
$finder = $this->laravel['view']->getFinder();

return (new Collection($finder->getPaths()))->merge(
(new Collection($finder->getHints()))->flatten()
);
}
}
