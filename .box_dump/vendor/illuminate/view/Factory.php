<?php

namespace Illuminate\View;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Illuminate\View\Engines\EngineResolver;
use InvalidArgumentException;

class Factory implements FactoryContract
{
use Macroable,
Concerns\ManagesComponents,
Concerns\ManagesEvents,
Concerns\ManagesFragments,
Concerns\ManagesLayouts,
Concerns\ManagesLoops,
Concerns\ManagesStacks,
Concerns\ManagesTranslations;






protected $engines;






protected $finder;






protected $events;






protected $container;






protected $shared = [];






protected $extensions = [
'blade.php' => 'blade',
'php' => 'php',
'css' => 'file',
'html' => 'file',
];






protected $composers = [];






protected $renderCount = 0;






protected $renderedOnce = [];






protected $pathEngineCache = [];






protected $normalizedNameCache = [];








public function __construct(EngineResolver $engines, ViewFinderInterface $finder, Dispatcher $events)
{
$this->finder = $finder;
$this->events = $events;
$this->engines = $engines;

$this->share('__env', $this);
}









public function file($path, $data = [], $mergeData = [])
{
$data = array_merge($mergeData, $this->parseData($data));

return tap($this->viewInstance($path, $path, $data), function ($view) {
$this->callCreator($view);
});
}









public function make($view, $data = [], $mergeData = [])
{
$path = $this->finder->find(
$view = $this->normalizeName($view)
);




$data = array_merge($mergeData, $this->parseData($data));

return tap($this->viewInstance($view, $path, $data), function ($view) {
$this->callCreator($view);
});
}











public function first(array $views, $data = [], $mergeData = [])
{
$view = Arr::first($views, function ($view) {
return $this->exists($view);
});

if (! $view) {
throw new InvalidArgumentException('None of the views in the given array exist.');
}

return $this->make($view, $data, $mergeData);
}










public function renderWhen($condition, $view, $data = [], $mergeData = [])
{
if (! $condition) {
return '';
}

return $this->make($view, $this->parseData($data), $mergeData)->render();
}










public function renderUnless($condition, $view, $data = [], $mergeData = [])
{
return $this->renderWhen(! $condition, $view, $data, $mergeData);
}










public function renderEach($view, $data, $iterator, $empty = 'raw|')
{
$result = '';




if (count($data) > 0) {
foreach ($data as $key => $value) {
$result .= $this->make(
$view, ['key' => $key, $iterator => $value]
)->render();
}
}




else {
$result = str_starts_with($empty, 'raw|')
? substr($empty, 4)
: $this->make($empty)->render();
}

return $result;
}







protected function normalizeName($name)
{
return $this->normalizedNameCache[$name] ??= ViewName::normalize($name);
}







protected function parseData($data)
{
return $data instanceof Arrayable ? $data->toArray() : $data;
}









protected function viewInstance($view, $path, $data)
{
return new View($this, $this->getEngineFromPath($path), $view, $path, $data);
}







public function exists($view)
{
try {
$this->finder->find($view);
} catch (InvalidArgumentException) {
return false;
}

return true;
}









public function getEngineFromPath($path)
{
if (isset($this->pathEngineCache[$path])) {
return $this->engines->resolve($this->pathEngineCache[$path]);
}

if (! $extension = $this->getExtension($path)) {
throw new InvalidArgumentException("Unrecognized extension in file: {$path}.");
}

return $this->engines->resolve(
$this->pathEngineCache[$path] = $this->extensions[$extension]
);
}







protected function getExtension($path)
{
$extensions = array_keys($this->extensions);

return Arr::first($extensions, function ($value) use ($path) {
return str_ends_with($path, '.'.$value);
});
}








public function share($key, $value = null)
{
$keys = is_array($key) ? $key : [$key => $value];

foreach ($keys as $key => $value) {
$this->shared[$key] = $value;
}

return $value;
}






public function incrementRender()
{
$this->renderCount++;
}






public function decrementRender()
{
$this->renderCount--;
}






public function doneRendering()
{
return $this->renderCount == 0;
}







public function hasRenderedOnce(string $id)
{
return isset($this->renderedOnce[$id]);
}







public function markAsRenderedOnce(string $id)
{
$this->renderedOnce[$id] = true;
}







public function addLocation($location)
{
$this->finder->addLocation($location);
}







public function prependLocation($location)
{
$this->finder->prependLocation($location);
}








public function addNamespace($namespace, $hints)
{
$this->finder->addNamespace($namespace, $hints);

return $this;
}








public function prependNamespace($namespace, $hints)
{
$this->finder->prependNamespace($namespace, $hints);

return $this;
}








public function replaceNamespace($namespace, $hints)
{
$this->finder->replaceNamespace($namespace, $hints);

return $this;
}









public function addExtension($extension, $engine, $resolver = null)
{
$this->finder->addExtension($extension);

if (isset($resolver)) {
$this->engines->register($engine, $resolver);
}

unset($this->extensions[$extension]);

$this->extensions = array_merge([$extension => $engine], $this->extensions);

$this->pathEngineCache = [];
}






public function flushState()
{
$this->renderCount = 0;
$this->renderedOnce = [];

$this->flushSections();
$this->flushStacks();
$this->flushComponents();
$this->flushFragments();
}






public function flushStateIfDoneRendering()
{
if ($this->doneRendering()) {
$this->flushState();
}
}






public function getExtensions()
{
return $this->extensions;
}






public function getEngineResolver()
{
return $this->engines;
}






public function getFinder()
{
return $this->finder;
}







public function setFinder(ViewFinderInterface $finder)
{
$this->finder = $finder;
}






public function flushFinderCache()
{
$this->getFinder()->flush();
}






public function getDispatcher()
{
return $this->events;
}







public function setDispatcher(Dispatcher $events)
{
$this->events = $events;
}






public function getContainer()
{
return $this->container;
}







public function setContainer(Container $container)
{
$this->container = $container;
}








public function shared($key, $default = null)
{
return Arr::get($this->shared, $key, $default);
}






public function getShared()
{
return $this->shared;
}
}
