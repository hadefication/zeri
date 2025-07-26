<?php

namespace Illuminate\View;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class Component
{





protected $except = [];






public $componentName;






public $attributes;






protected static $factory;






protected static $componentsResolver;






protected static $bladeViewCache = [];






protected static $propertyCache = [];






protected static $methodCache = [];






protected static $constructorParametersCache = [];






protected static $ignoredParameterNames = [];






abstract public function render();







public static function resolve($data)
{
if (static::$componentsResolver) {
return call_user_func(static::$componentsResolver, static::class, $data);
}

$parameters = static::extractConstructorParameters();

$dataKeys = array_keys($data);

if (empty(array_diff($parameters, $dataKeys))) {
return new static(...array_intersect_key($data, array_flip($parameters)));
}

return Container::getInstance()->make(static::class, $data);
}






protected static function extractConstructorParameters()
{
if (! isset(static::$constructorParametersCache[static::class])) {
$class = new ReflectionClass(static::class);

$constructor = $class->getConstructor();

static::$constructorParametersCache[static::class] = $constructor
? (new Collection($constructor->getParameters()))->map->getName()->all()
: [];
}

return static::$constructorParametersCache[static::class];
}






public function resolveView()
{
$view = $this->render();

if ($view instanceof ViewContract) {
return $view;
}

if ($view instanceof Htmlable) {
return $view;
}

$resolver = function ($view) {
if ($view instanceof ViewContract) {
return $view;
}

return $this->extractBladeViewFromString($view);
};

return $view instanceof Closure ? function (array $data = []) use ($view, $resolver) {
return $resolver($view($data));
}
: $resolver($view);
}







protected function extractBladeViewFromString($contents)
{
$key = sprintf('%s::%s', static::class, $contents);

if (isset(static::$bladeViewCache[$key])) {
return static::$bladeViewCache[$key];
}

if ($this->factory()->exists($contents)) {
return static::$bladeViewCache[$key] = $contents;
}

return static::$bladeViewCache[$key] = $this->createBladeViewFromString($this->factory(), $contents);
}








protected function createBladeViewFromString($factory, $contents)
{
$factory->addNamespace(
'__components',
$directory = Container::getInstance()['config']->get('view.compiled')
);

if (! is_file($viewFile = $directory.'/'.hash('xxh128', $contents).'.blade.php')) {
if (! is_dir($directory)) {
mkdir($directory, 0755, true);
}

file_put_contents($viewFile, $contents);
}

return '__components::'.basename($viewFile, '.blade.php');
}









public function data()
{
$this->attributes = $this->attributes ?: $this->newAttributeBag();

return array_merge($this->extractPublicProperties(), $this->extractPublicMethods());
}






protected function extractPublicProperties()
{
$class = get_class($this);

if (! isset(static::$propertyCache[$class])) {
$reflection = new ReflectionClass($this);

static::$propertyCache[$class] = (new Collection($reflection->getProperties(ReflectionProperty::IS_PUBLIC)))
->reject(fn (ReflectionProperty $property) => $property->isStatic())
->reject(fn (ReflectionProperty $property) => $this->shouldIgnore($property->getName()))
->map(fn (ReflectionProperty $property) => $property->getName())
->all();
}

$values = [];

foreach (static::$propertyCache[$class] as $property) {
$values[$property] = $this->{$property};
}

return $values;
}






protected function extractPublicMethods()
{
$class = get_class($this);

if (! isset(static::$methodCache[$class])) {
$reflection = new ReflectionClass($this);

static::$methodCache[$class] = (new Collection($reflection->getMethods(ReflectionMethod::IS_PUBLIC)))
->reject(fn (ReflectionMethod $method) => $this->shouldIgnore($method->getName()))
->map(fn (ReflectionMethod $method) => $method->getName());
}

$values = [];

foreach (static::$methodCache[$class] as $method) {
$values[$method] = $this->createVariableFromMethod(new ReflectionMethod($this, $method));
}

return $values;
}







protected function createVariableFromMethod(ReflectionMethod $method)
{
return $method->getNumberOfParameters() === 0
? $this->createInvokableVariable($method->getName())
: Closure::fromCallable([$this, $method->getName()]);
}







protected function createInvokableVariable(string $method)
{
return new InvokableComponentVariable(function () use ($method) {
return $this->{$method}();
});
}







protected function shouldIgnore($name)
{
return str_starts_with($name, '__') ||
in_array($name, $this->ignoredMethods());
}






protected function ignoredMethods()
{
return array_merge([
'data',
'render',
'resolve',
'resolveView',
'shouldRender',
'view',
'withName',
'withAttributes',
'flushCache',
'forgetFactory',
'forgetComponentsResolver',
'resolveComponentsUsing',
], $this->except);
}







public function withName($name)
{
$this->componentName = $name;

return $this;
}







public function withAttributes(array $attributes)
{
$this->attributes = $this->attributes ?: $this->newAttributeBag();

$this->attributes->setAttributes($attributes);

return $this;
}







protected function newAttributeBag(array $attributes = [])
{
return new ComponentAttributeBag($attributes);
}






public function shouldRender()
{
return true;
}









public function view($view, $data = [], $mergeData = [])
{
return $this->factory()->make($view, $data, $mergeData);
}






protected function factory()
{
if (is_null(static::$factory)) {
static::$factory = Container::getInstance()->make('view');
}

return static::$factory;
}






public static function ignoredParameterNames()
{
if (! isset(static::$ignoredParameterNames[static::class])) {
$constructor = (new ReflectionClass(
static::class
))->getConstructor();

if (! $constructor) {
return static::$ignoredParameterNames[static::class] = [];
}

static::$ignoredParameterNames[static::class] = (new Collection($constructor->getParameters()))
->map
->getName()
->all();
}

return static::$ignoredParameterNames[static::class];
}






public static function flushCache()
{
static::$bladeViewCache = [];
static::$constructorParametersCache = [];
static::$methodCache = [];
static::$propertyCache = [];
}






public static function forgetFactory()
{
static::$factory = null;
}








public static function forgetComponentsResolver()
{
static::$componentsResolver = null;
}









public static function resolveComponentsUsing($resolver)
{
static::$componentsResolver = $resolver;
}
}
