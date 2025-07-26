<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function realpath;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use SplObjectStorage;

/**
@no-named-arguments


*/
final class SourceMapper
{



private static ?SplObjectStorage $files = null;




public function map(Source $source): array
{
if (self::$files === null) {
self::$files = new SplObjectStorage;
}

if (isset(self::$files[$source])) {
return self::$files[$source];
}

$files = [];

$directories = $this->aggregateDirectories($source->includeDirectories());

foreach ($directories as $path => [$prefixes, $suffixes]) {
foreach ((new FileIteratorFacade)->getFilesAsArray($path, $suffixes, $prefixes) as $file) {
$file = realpath($file);

if (!$file) {
continue;
}

$files[$file] = true;
}
}

foreach ($source->includeFiles() as $file) {
$file = realpath($file->path());

if (!$file) {
continue;
}

$files[$file] = true;
}

$directories = $this->aggregateDirectories($source->excludeDirectories());

foreach ($directories as $path => [$prefixes, $suffixes]) {
foreach ((new FileIteratorFacade)->getFilesAsArray($path, $suffixes, $prefixes) as $file) {
$file = realpath($file);

if (!$file) {
continue;
}

if (!isset($files[$file])) {
continue;
}

unset($files[$file]);
}
}

foreach ($source->excludeFiles() as $file) {
$file = realpath($file->path());

if (!$file) {
continue;
}

if (!isset($files[$file])) {
continue;
}

unset($files[$file]);
}

self::$files[$source] = $files;

return $files;
}




private function aggregateDirectories(FilterDirectoryCollection $directories): array
{
$aggregated = [];

foreach ($directories as $directory) {
if (!isset($aggregated[$directory->path()])) {
$aggregated[$directory->path()] = [
0 => [],
1 => [],
];
}

$prefix = $directory->prefix();

if ($prefix !== '') {
$aggregated[$directory->path()][0][] = $prefix;
}

$suffix = $directory->suffix();

if ($suffix !== '') {
$aggregated[$directory->path()][1][] = $suffix;
}
}

return $aggregated;
}
}
