<?php

namespace Illuminate\Foundation\Events;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Reflector;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class DiscoverEvents
{





public static $guessClassNamesUsingCallback;








public static function within($listenerPath, $basePath)
{
if (Arr::wrap($listenerPath) === []) {
return [];
}

$listeners = new Collection(static::getListenerEvents(
Finder::create()->files()->in($listenerPath), $basePath
));

$discoveredEvents = [];

foreach ($listeners as $listener => $events) {
foreach ($events as $event) {
if (! isset($discoveredEvents[$event])) {
$discoveredEvents[$event] = [];
}

$discoveredEvents[$event][] = $listener;
}
}

return $discoveredEvents;
}








protected static function getListenerEvents($listeners, $basePath)
{
$listenerEvents = [];

foreach ($listeners as $listener) {
try {
$listener = new ReflectionClass(
static::classFromFile($listener, $basePath)
);
} catch (ReflectionException) {
continue;
}

if (! $listener->isInstantiable()) {
continue;
}

foreach ($listener->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
if ((! Str::is('handle*', $method->name) && ! Str::is('__invoke', $method->name)) ||
! isset($method->getParameters()[0])) {
continue;
}

$listenerEvents[$listener->name.'@'.$method->name] =
Reflector::getParameterClassNames($method->getParameters()[0]);
}
}

return array_filter($listenerEvents);
}








protected static function classFromFile(SplFileInfo $file, $basePath)
{
if (static::$guessClassNamesUsingCallback) {
return call_user_func(static::$guessClassNamesUsingCallback, $file, $basePath);
}

$class = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

return ucfirst(Str::camel(str_replace(
[DIRECTORY_SEPARATOR, ucfirst(basename(app()->path())).'\\'],
['\\', app()->getNamespace()],
ucfirst(Str::replaceLast('.php', '', $class))
)));
}







public static function guessClassNamesUsing(callable $callback)
{
static::$guessClassNamesUsingCallback = $callback;
}
}
