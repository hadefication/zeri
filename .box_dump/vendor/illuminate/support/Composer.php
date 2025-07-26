<?php

namespace Illuminate\Support;

use Closure;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Composer
{





protected $files;






protected $workingPath;







public function __construct(Filesystem $files, $workingPath = null)
{
$this->files = $files;
$this->workingPath = $workingPath;
}









public function hasPackage($package)
{
$composer = json_decode(file_get_contents($this->findComposerFile()), true);

return array_key_exists($package, $composer['require'] ?? [])
|| array_key_exists($package, $composer['require-dev'] ?? []);
}










public function requirePackages(array $packages, bool $dev = false, Closure|OutputInterface|null $output = null, $composerBinary = null)
{
$command = (new Collection([
...$this->findComposer($composerBinary),
'require',
...$packages,
]))
->when($dev, function ($command) {
$command->push('--dev');
})->all();

return 0 === $this->getProcess($command, ['COMPOSER_MEMORY_LIMIT' => '-1'])
->run(
$output instanceof OutputInterface
? function ($type, $line) use ($output) {
$output->write('    '.$line);
} : $output
);
}










public function removePackages(array $packages, bool $dev = false, Closure|OutputInterface|null $output = null, $composerBinary = null)
{
$command = (new Collection([
...$this->findComposer($composerBinary),
'remove',
...$packages,
]))
->when($dev, function ($command) {
$command->push('--dev');
})->all();

return 0 === $this->getProcess($command, ['COMPOSER_MEMORY_LIMIT' => '-1'])
->run(
$output instanceof OutputInterface
? function ($type, $line) use ($output) {
$output->write('    '.$line);
} : $output
);
}









public function modify(callable $callback)
{
$composerFile = $this->findComposerFile();

$composer = json_decode(file_get_contents($composerFile), true, 512, JSON_THROW_ON_ERROR);

file_put_contents(
$composerFile,
json_encode(
call_user_func($callback, $composer),
JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
)
);
}








public function dumpAutoloads($extra = '', $composerBinary = null)
{
$extra = $extra ? (array) $extra : [];

$command = array_merge($this->findComposer($composerBinary), ['dump-autoload'], $extra);

return $this->getProcess($command)->run();
}







public function dumpOptimized($composerBinary = null)
{
return $this->dumpAutoloads('--optimize', $composerBinary);
}







public function findComposer($composerBinary = null)
{
if (! is_null($composerBinary) && $this->files->exists($composerBinary)) {
return [$this->phpBinary(), $composerBinary];
} elseif ($this->files->exists($this->workingPath.'/composer.phar')) {
return [$this->phpBinary(), 'composer.phar'];
}

return ['composer'];
}








protected function findComposerFile()
{
$composerFile = "{$this->workingPath}/composer.json";

if (! file_exists($composerFile)) {
throw new RuntimeException("Unable to locate `composer.json` file at [{$this->workingPath}].");
}

return $composerFile;
}






protected function phpBinary()
{
return php_binary();
}








protected function getProcess(array $command, array $env = [])
{
return (new Process($command, $this->workingPath, $env))->setTimeout(null);
}







public function setWorkingPath($path)
{
$this->workingPath = realpath($path);

return $this;
}






public function getVersion()
{
$command = array_merge($this->findComposer(), ['-V', '--no-ansi']);

$process = $this->getProcess($command);

$process->run();

$output = $process->getOutput();

if (preg_match('/(\d+(\.\d+){2})/', $output, $version)) {
return $version[1];
}

return explode(' ', $output)[2] ?? null;
}
}
