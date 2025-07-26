<?php

namespace Illuminate\View;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;

class FileViewFinder implements ViewFinderInterface
{





protected $files;






protected $paths;






protected $views = [];






protected $hints = [];






protected $extensions = ['blade.php', 'php', 'css', 'html'];








public function __construct(Filesystem $files, array $paths, ?array $extensions = null)
{
$this->files = $files;
$this->paths = array_map($this->resolvePath(...), $paths);

if (isset($extensions)) {
$this->extensions = $extensions;
}
}







public function find($name)
{
if (isset($this->views[$name])) {
return $this->views[$name];
}

if ($this->hasHintInformation($name = trim($name))) {
return $this->views[$name] = $this->findNamespacedView($name);
}

return $this->views[$name] = $this->findInPaths($name, $this->paths);
}







protected function findNamespacedView($name)
{
[$namespace, $view] = $this->parseNamespaceSegments($name);

return $this->findInPaths($view, $this->hints[$namespace]);
}









protected function parseNamespaceSegments($name)
{
$segments = explode(static::HINT_PATH_DELIMITER, $name);

if (count($segments) !== 2) {
throw new InvalidArgumentException("View [{$name}] has an invalid name.");
}

if (! isset($this->hints[$segments[0]])) {
throw new InvalidArgumentException("No hint path defined for [{$segments[0]}].");
}

return $segments;
}










protected function findInPaths($name, $paths)
{
foreach ((array) $paths as $path) {
foreach ($this->getPossibleViewFiles($name) as $file) {
$viewPath = $path.'/'.$file;

if (strlen($viewPath) < (PHP_MAXPATHLEN - 1) && $this->files->exists($viewPath)) {
return $viewPath;
}
}
}

throw new InvalidArgumentException("View [{$name}] not found.");
}







protected function getPossibleViewFiles($name)
{
return array_map(fn ($extension) => str_replace('.', '/', $name).'.'.$extension, $this->extensions);
}







public function addLocation($location)
{
$this->paths[] = $this->resolvePath($location);
}







public function prependLocation($location)
{
array_unshift($this->paths, $this->resolvePath($location));
}







protected function resolvePath($path)
{
return realpath($path) ?: $path;
}








public function addNamespace($namespace, $hints)
{
$hints = (array) $hints;

if (isset($this->hints[$namespace])) {
$hints = array_merge($this->hints[$namespace], $hints);
}

$this->hints[$namespace] = $hints;
}








public function prependNamespace($namespace, $hints)
{
$hints = (array) $hints;

if (isset($this->hints[$namespace])) {
$hints = array_merge($hints, $this->hints[$namespace]);
}

$this->hints[$namespace] = $hints;
}








public function replaceNamespace($namespace, $hints)
{
$this->hints[$namespace] = (array) $hints;
}







public function addExtension($extension)
{
if (($index = array_search($extension, $this->extensions)) !== false) {
unset($this->extensions[$index]);
}

array_unshift($this->extensions, $extension);
}







public function hasHintInformation($name)
{
return strpos($name, static::HINT_PATH_DELIMITER) > 0;
}






public function flush()
{
$this->views = [];
}






public function getFilesystem()
{
return $this->files;
}







public function setPaths($paths)
{
$this->paths = $paths;

return $this;
}






public function getPaths()
{
return $this->paths;
}






public function getViews()
{
return $this->views;
}






public function getHints()
{
return $this->hints;
}






public function getExtensions()
{
return $this->extensions;
}
}
