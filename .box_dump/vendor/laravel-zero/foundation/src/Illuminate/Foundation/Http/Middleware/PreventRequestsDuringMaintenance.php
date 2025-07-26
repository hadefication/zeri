<?php

namespace Illuminate\Foundation\Http\Middleware;

use Closure;
use ErrorException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\MaintenanceModeBypassCookie;
use Illuminate\Foundation\Http\Middleware\Concerns\ExcludesPaths;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventRequestsDuringMaintenance
{
use ExcludesPaths;






protected $app;






protected $except = [];






protected static $neverPrevent = [];






public function __construct(Application $app)
{
$this->app = $app;
}











public function handle($request, Closure $next)
{
if ($this->inExceptArray($request)) {
return $next($request);
}

if ($this->app->maintenanceMode()->active()) {
try {
$data = $this->app->maintenanceMode()->data();
} catch (ErrorException $exception) {
if (! $this->app->maintenanceMode()->active()) {
return $next($request);
}

throw $exception;
}

if (isset($data['secret']) && $request->path() === $data['secret']) {
return $this->bypassResponse($data['secret']);
}

if ($this->hasValidBypassCookie($request, $data)) {
return $next($request);
}

if (isset($data['redirect'])) {
$path = $data['redirect'] === '/'
? $data['redirect']
: trim($data['redirect'], '/');

if ($request->path() !== $path) {
return redirect($path);
}
}

if (isset($data['template'])) {
return response(
$data['template'],
$data['status'] ?? 503,
$this->getHeaders($data)
);
}

throw new HttpException(
$data['status'] ?? 503,
'Service Unavailable',
null,
$this->getHeaders($data)
);
}

return $next($request);
}








protected function hasValidBypassCookie($request, array $data)
{
return isset($data['secret']) &&
$request->cookie('laravel_maintenance') &&
MaintenanceModeBypassCookie::isValid(
$request->cookie('laravel_maintenance'),
$data['secret']
);
}







protected function bypassResponse(string $secret)
{
return redirect('/')->withCookie(
MaintenanceModeBypassCookie::create($secret)
);
}







protected function getHeaders($data)
{
$headers = isset($data['retry']) ? ['Retry-After' => $data['retry']] : [];

if (isset($data['refresh'])) {
$headers['Refresh'] = $data['refresh'];
}

return $headers;
}






public function getExcludedPaths()
{
return array_merge($this->except, static::$neverPrevent);
}







public static function except($uris)
{
static::$neverPrevent = array_values(array_unique(
array_merge(static::$neverPrevent, Arr::wrap($uris))
));
}






public static function flushState()
{
static::$neverPrevent = [];
}
}
