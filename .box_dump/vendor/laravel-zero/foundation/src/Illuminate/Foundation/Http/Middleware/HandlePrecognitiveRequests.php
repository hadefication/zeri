<?php

namespace Illuminate\Foundation\Http\Middleware;

use Illuminate\Container\Container;
use Illuminate\Foundation\Routing\PrecognitionCallableDispatcher;
use Illuminate\Foundation\Routing\PrecognitionControllerDispatcher;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;

class HandlePrecognitiveRequests
{





protected $container;






public function __construct(Container $container)
{
$this->container = $container;
}








public function handle($request, $next)
{
if (! $request->isAttemptingPrecognition()) {
return $this->appendVaryHeader($request, $next($request));
}

$this->prepareForPrecognition($request);

return tap($next($request), function ($response) use ($request) {
$response->headers->set('Precognition', 'true');

$this->appendVaryHeader($request, $response);
});
}







protected function prepareForPrecognition($request)
{
$request->attributes->set('precognitive', true);

$this->container->bind(CallableDispatcherContract::class, fn ($app) => new PrecognitionCallableDispatcher($app));
$this->container->bind(ControllerDispatcherContract::class, fn ($app) => new PrecognitionControllerDispatcher($app));
}








protected function appendVaryHeader($request, $response)
{
return tap($response, fn () => $response->headers->set('Vary', implode(', ', array_filter([
$response->headers->get('Vary'),
'Precognition',
]))));
}
}
