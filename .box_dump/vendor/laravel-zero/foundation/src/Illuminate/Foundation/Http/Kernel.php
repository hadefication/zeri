<?php

namespace Illuminate\Foundation\Http;

use Carbon\CarbonInterval;
use DateTimeInterface;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel as KernelContract;
use Illuminate\Foundation\Events\Terminating;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Router;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\InteractsWithTime;
use InvalidArgumentException;
use Throwable;

class Kernel implements KernelContract
{
use InteractsWithTime;






protected $app;






protected $router;






protected $bootstrappers = [
\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
\Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
\Illuminate\Foundation\Bootstrap\HandleExceptions::class,
\Illuminate\Foundation\Bootstrap\RegisterFacades::class,
\Illuminate\Foundation\Bootstrap\RegisterProviders::class,
\Illuminate\Foundation\Bootstrap\BootProviders::class,
];






protected $middleware = [];






protected $middlewareGroups = [];








protected $routeMiddleware = [];






protected $middlewareAliases = [];






protected $requestLifecycleDurationHandlers = [];






protected $requestStartedAt;








protected $middlewarePriority = [
\Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
\Illuminate\Cookie\Middleware\EncryptCookies::class,
\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
\Illuminate\Session\Middleware\StartSession::class,
\Illuminate\View\Middleware\ShareErrorsFromSession::class,
\Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
\Illuminate\Routing\Middleware\ThrottleRequests::class,
\Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
\Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
\Illuminate\Routing\Middleware\SubstituteBindings::class,
\Illuminate\Auth\Middleware\Authorize::class,
];







public function __construct(Application $app, Router $router)
{
$this->app = $app;
$this->router = $router;

$this->syncMiddlewareToRouter();
}







public function handle($request)
{
$this->requestStartedAt = Carbon::now();

try {
$request->enableHttpMethodParameterOverride();

$response = $this->sendRequestThroughRouter($request);
} catch (Throwable $e) {
$this->reportException($e);

$response = $this->renderException($request, $e);
}

$this->app['events']->dispatch(
new RequestHandled($request, $response)
);

return $response;
}







protected function sendRequestThroughRouter($request)
{
$this->app->instance('request', $request);

Facade::clearResolvedInstance('request');

$this->bootstrap();

return (new Pipeline($this->app))
->send($request)
->through($this->app->shouldSkipMiddleware() ? [] : $this->middleware)
->then($this->dispatchToRouter());
}






public function bootstrap()
{
if (! $this->app->hasBeenBootstrapped()) {
$this->app->bootstrapWith($this->bootstrappers());
}
}






protected function dispatchToRouter()
{
return function ($request) {
$this->app->instance('request', $request);

return $this->router->dispatch($request);
};
}








public function terminate($request, $response)
{
$this->app['events']->dispatch(new Terminating);

$this->terminateMiddleware($request, $response);

$this->app->terminate();

if ($this->requestStartedAt === null) {
return;
}

$this->requestStartedAt->setTimezone($this->app['config']->get('app.timezone') ?? 'UTC');

foreach ($this->requestLifecycleDurationHandlers as ['threshold' => $threshold, 'handler' => $handler]) {
$end ??= Carbon::now();

if ($this->requestStartedAt->diffInMilliseconds($end) > $threshold) {
$handler($this->requestStartedAt, $request, $response);
}
}

$this->requestStartedAt = null;
}








protected function terminateMiddleware($request, $response)
{
$middlewares = $this->app->shouldSkipMiddleware() ? [] : array_merge(
$this->gatherRouteMiddleware($request),
$this->middleware
);

foreach ($middlewares as $middleware) {
if (! is_string($middleware)) {
continue;
}

[$name] = $this->parseMiddleware($middleware);

$instance = $this->app->make($name);

if (method_exists($instance, 'terminate')) {
$instance->terminate($request, $response);
}
}
}








public function whenRequestLifecycleIsLongerThan($threshold, $handler)
{
$threshold = $threshold instanceof DateTimeInterface
? $this->secondsUntil($threshold) * 1000
: $threshold;

$threshold = $threshold instanceof CarbonInterval
? $threshold->totalMilliseconds
: $threshold;

$this->requestLifecycleDurationHandlers[] = [
'threshold' => $threshold,
'handler' => $handler,
];
}






public function requestStartedAt()
{
return $this->requestStartedAt;
}







protected function gatherRouteMiddleware($request)
{
if ($route = $request->route()) {
return $this->router->gatherRouteMiddleware($route);
}

return [];
}







protected function parseMiddleware($middleware)
{
[$name, $parameters] = array_pad(explode(':', $middleware, 2), 2, []);

if (is_string($parameters)) {
$parameters = explode(',', $parameters);
}

return [$name, $parameters];
}







public function hasMiddleware($middleware)
{
return in_array($middleware, $this->middleware);
}







public function prependMiddleware($middleware)
{
if (array_search($middleware, $this->middleware) === false) {
array_unshift($this->middleware, $middleware);
}

return $this;
}







public function pushMiddleware($middleware)
{
if (array_search($middleware, $this->middleware) === false) {
$this->middleware[] = $middleware;
}

return $this;
}










public function prependMiddlewareToGroup($group, $middleware)
{
if (! isset($this->middlewareGroups[$group])) {
throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
}

if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
array_unshift($this->middlewareGroups[$group], $middleware);
}

$this->syncMiddlewareToRouter();

return $this;
}










public function appendMiddlewareToGroup($group, $middleware)
{
if (! isset($this->middlewareGroups[$group])) {
throw new InvalidArgumentException("The [{$group}] middleware group has not been defined.");
}

if (array_search($middleware, $this->middlewareGroups[$group]) === false) {
$this->middlewareGroups[$group][] = $middleware;
}

$this->syncMiddlewareToRouter();

return $this;
}







public function prependToMiddlewarePriority($middleware)
{
if (! in_array($middleware, $this->middlewarePriority)) {
array_unshift($this->middlewarePriority, $middleware);
}

$this->syncMiddlewareToRouter();

return $this;
}







public function appendToMiddlewarePriority($middleware)
{
if (! in_array($middleware, $this->middlewarePriority)) {
$this->middlewarePriority[] = $middleware;
}

$this->syncMiddlewareToRouter();

return $this;
}








public function addToMiddlewarePriorityBefore($before, $middleware)
{
return $this->addToMiddlewarePriorityRelative($before, $middleware, after: false);
}








public function addToMiddlewarePriorityAfter($after, $middleware)
{
return $this->addToMiddlewarePriorityRelative($after, $middleware);
}









protected function addToMiddlewarePriorityRelative($existing, $middleware, $after = true)
{
if (! in_array($middleware, $this->middlewarePriority)) {
$index = $after ? 0 : count($this->middlewarePriority);

foreach ((array) $existing as $existingMiddleware) {
if (in_array($existingMiddleware, $this->middlewarePriority)) {
$middlewareIndex = array_search($existingMiddleware, $this->middlewarePriority);

if ($after && $middlewareIndex > $index) {
$index = $middlewareIndex + 1;
} elseif ($after === false && $middlewareIndex < $index) {
$index = $middlewareIndex;
}
}
}

if ($index === 0 && $after === false) {
array_unshift($this->middlewarePriority, $middleware);
} elseif (($after && $index === 0) || $index === count($this->middlewarePriority)) {
$this->middlewarePriority[] = $middleware;
} else {
array_splice($this->middlewarePriority, $index, 0, $middleware);
}
}

$this->syncMiddlewareToRouter();

return $this;
}






protected function syncMiddlewareToRouter()
{
$this->router->middlewarePriority = $this->middlewarePriority;

foreach ($this->middlewareGroups as $key => $middleware) {
$this->router->middlewareGroup($key, $middleware);
}

foreach (array_merge($this->routeMiddleware, $this->middlewareAliases) as $key => $middleware) {
$this->router->aliasMiddleware($key, $middleware);
}
}






public function getMiddlewarePriority()
{
return $this->middlewarePriority;
}






protected function bootstrappers()
{
return $this->bootstrappers;
}







protected function reportException(Throwable $e)
{
$this->app[ExceptionHandler::class]->report($e);
}








protected function renderException($request, Throwable $e)
{
return $this->app[ExceptionHandler::class]->render($request, $e);
}






public function getGlobalMiddleware()
{
return $this->middleware;
}







public function setGlobalMiddleware(array $middleware)
{
$this->middleware = $middleware;

$this->syncMiddlewareToRouter();

return $this;
}






public function getMiddlewareGroups()
{
return $this->middlewareGroups;
}







public function setMiddlewareGroups(array $groups)
{
$this->middlewareGroups = $groups;

$this->syncMiddlewareToRouter();

return $this;
}








public function getRouteMiddleware()
{
return $this->getMiddlewareAliases();
}






public function getMiddlewareAliases()
{
return array_merge($this->routeMiddleware, $this->middlewareAliases);
}







public function setMiddlewareAliases(array $aliases)
{
$this->middlewareAliases = $aliases;

$this->syncMiddlewareToRouter();

return $this;
}







public function setMiddlewarePriority(array $priority)
{
$this->middlewarePriority = $priority;

$this->syncMiddlewareToRouter();

return $this;
}






public function getApplication()
{
return $this->app;
}







public function setApplication(Application $app)
{
$this->app = $app;

return $this;
}
}
