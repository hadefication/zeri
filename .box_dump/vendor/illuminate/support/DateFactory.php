<?php

namespace Illuminate\Support;

use Carbon\Factory;
use InvalidArgumentException;
































































































class DateFactory
{





const DEFAULT_CLASS_NAME = Carbon::class;






protected static $dateClass;






protected static $callable;






protected static $factory;









public static function use($handler)
{
if (is_callable($handler) && is_object($handler)) {
return static::useCallable($handler);
} elseif (is_string($handler)) {
return static::useClass($handler);
} elseif ($handler instanceof Factory) {
return static::useFactory($handler);
}

throw new InvalidArgumentException('Invalid date creation handler. Please provide a class name, callable, or Carbon factory.');
}






public static function useDefault()
{
static::$dateClass = null;
static::$callable = null;
static::$factory = null;
}







public static function useCallable(callable $callable)
{
static::$callable = $callable;

static::$dateClass = null;
static::$factory = null;
}







public static function useClass($dateClass)
{
static::$dateClass = $dateClass;

static::$factory = null;
static::$callable = null;
}







public static function useFactory($factory)
{
static::$factory = $factory;

static::$dateClass = null;
static::$callable = null;
}










public function __call($method, $parameters)
{
$defaultClassName = static::DEFAULT_CLASS_NAME;


if (static::$callable) {
return call_user_func(static::$callable, $defaultClassName::$method(...$parameters));
}


if (static::$factory) {
return static::$factory->$method(...$parameters);
}

$dateClass = static::$dateClass ?: $defaultClassName;


if (method_exists($dateClass, $method) ||
method_exists($dateClass, 'hasMacro') && $dateClass::hasMacro($method)) {
return $dateClass::$method(...$parameters);
}


$date = $defaultClassName::$method(...$parameters);


if (method_exists($dateClass, 'instance')) {
return $dateClass::instance($date);
}


return new $dateClass($date->format('Y-m-d H:i:s.u'), $date->getTimezone());
}
}
