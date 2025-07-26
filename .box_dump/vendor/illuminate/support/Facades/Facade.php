<?php

namespace Illuminate\Support\Facades;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Js;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\Fake;
use Illuminate\Support\Uri;
use Mockery;
use Mockery\LegacyMockInterface;
use RuntimeException;

abstract class Facade
{





protected static $app;






protected static $resolvedInstance;






protected static $cached = true;







public static function resolved(Closure $callback)
{
$accessor = static::getFacadeAccessor();

if (static::$app->resolved($accessor) === true) {
$callback(static::getFacadeRoot(), static::$app);
}

static::$app->afterResolving($accessor, function ($service, $app) use ($callback) {
$callback($service, $app);
});
}






public static function spy()
{
if (! static::isMock()) {
$class = static::getMockableClass();

return tap($class ? Mockery::spy($class) : Mockery::spy(), function ($spy) {
static::swap($spy);
});
}
}






public static function partialMock()
{
$name = static::getFacadeAccessor();

$mock = static::isMock()
? static::$resolvedInstance[$name]
: static::createFreshMockInstance();

return $mock->makePartial();
}






public static function shouldReceive()
{
$name = static::getFacadeAccessor();

$mock = static::isMock()
? static::$resolvedInstance[$name]
: static::createFreshMockInstance();

return $mock->shouldReceive(...func_get_args());
}






public static function expects()
{
$name = static::getFacadeAccessor();

$mock = static::isMock()
? static::$resolvedInstance[$name]
: static::createFreshMockInstance();

return $mock->expects(...func_get_args());
}






protected static function createFreshMockInstance()
{
return tap(static::createMock(), function ($mock) {
static::swap($mock);

$mock->shouldAllowMockingProtectedMethods();
});
}






protected static function createMock()
{
$class = static::getMockableClass();

return $class ? Mockery::mock($class) : Mockery::mock();
}






protected static function isMock()
{
$name = static::getFacadeAccessor();

return isset(static::$resolvedInstance[$name]) &&
static::$resolvedInstance[$name] instanceof LegacyMockInterface;
}






protected static function getMockableClass()
{
if ($root = static::getFacadeRoot()) {
return get_class($root);
}
}







public static function swap($instance)
{
static::$resolvedInstance[static::getFacadeAccessor()] = $instance;

if (isset(static::$app)) {
static::$app->instance(static::getFacadeAccessor(), $instance);
}
}






public static function isFake()
{
$name = static::getFacadeAccessor();

return isset(static::$resolvedInstance[$name]) &&
static::$resolvedInstance[$name] instanceof Fake;
}






public static function getFacadeRoot()
{
return static::resolveFacadeInstance(static::getFacadeAccessor());
}








protected static function getFacadeAccessor()
{
throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
}







protected static function resolveFacadeInstance($name)
{
if (isset(static::$resolvedInstance[$name])) {
return static::$resolvedInstance[$name];
}

if (static::$app) {
if (static::$cached) {
return static::$resolvedInstance[$name] = static::$app[$name];
}

return static::$app[$name];
}
}







public static function clearResolvedInstance($name)
{
unset(static::$resolvedInstance[$name]);
}






public static function clearResolvedInstances()
{
static::$resolvedInstance = [];
}






public static function defaultAliases()
{
return new Collection([
'App' => App::class,
'Arr' => Arr::class,
'Artisan' => Artisan::class,
'Auth' => Auth::class,
'Blade' => Blade::class,
'Broadcast' => Broadcast::class,
'Bus' => Bus::class,
'Cache' => Cache::class,
'Concurrency' => Concurrency::class,
'Config' => Config::class,
'Context' => Context::class,
'Cookie' => Cookie::class,
'Crypt' => Crypt::class,
'Date' => Date::class,
'DB' => DB::class,
'Eloquent' => Model::class,
'Event' => Event::class,
'File' => File::class,
'Gate' => Gate::class,
'Hash' => Hash::class,
'Http' => Http::class,
'Js' => Js::class,
'Lang' => Lang::class,
'Log' => Log::class,
'Mail' => Mail::class,
'Notification' => Notification::class,
'Number' => Number::class,
'Password' => Password::class,
'Process' => Process::class,
'Queue' => Queue::class,
'RateLimiter' => RateLimiter::class,
'Redirect' => Redirect::class,
'Request' => Request::class,
'Response' => Response::class,
'Route' => Route::class,
'Schedule' => Schedule::class,
'Schema' => Schema::class,
'Session' => Session::class,
'Storage' => Storage::class,
'Str' => Str::class,
'URL' => URL::class,
'Uri' => Uri::class,
'Validator' => Validator::class,
'View' => View::class,
'Vite' => Vite::class,
]);
}






public static function getFacadeApplication()
{
return static::$app;
}







public static function setFacadeApplication($app)
{
static::$app = $app;
}










public static function __callStatic($method, $args)
{
$instance = static::getFacadeRoot();

if (! $instance) {
throw new RuntimeException('A facade root has not been set.');
}

return $instance->$method(...$args);
}
}
