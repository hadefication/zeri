<?php

namespace Illuminate\View\Compilers;

use ErrorException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class Compiler
{





protected $files;






protected $cachePath;






protected $basePath;






protected $shouldCache;






protected $compiledExtension = 'php';






protected $shouldCheckTimestamps;













public function __construct(
Filesystem $files,
$cachePath,
$basePath = '',
$shouldCache = true,
$compiledExtension = 'php',
$shouldCheckTimestamps = true,
) {
if (! $cachePath) {
throw new InvalidArgumentException('Please provide a valid cache path.');
}

$this->files = $files;
$this->cachePath = $cachePath;
$this->basePath = $basePath;
$this->shouldCache = $shouldCache;
$this->compiledExtension = $compiledExtension;
$this->shouldCheckTimestamps = $shouldCheckTimestamps;
}







public function getCompiledPath($path)
{
return $this->cachePath.'/'.hash('xxh128', 'v2'.Str::after($path, $this->basePath)).'.'.$this->compiledExtension;
}









public function isExpired($path)
{
if (! $this->shouldCache) {
return true;
}

$compiled = $this->getCompiledPath($path);




if (! $this->files->exists($compiled)) {
return true;
}

if (! $this->shouldCheckTimestamps) {
return false;
}

try {
return $this->files->lastModified($path) >=
$this->files->lastModified($compiled);
} catch (ErrorException $exception) {
if (! $this->files->exists($compiled)) {
return true;
}

throw $exception;
}
}







protected function ensureCompiledDirectoryExists($path)
{
if (! $this->files->exists(dirname($path))) {
$this->files->makeDirectory(dirname($path), 0777, true, true);
}
}
}
