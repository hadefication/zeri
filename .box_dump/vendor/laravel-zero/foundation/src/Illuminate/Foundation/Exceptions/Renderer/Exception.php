<?php

namespace Illuminate\Foundation\Exceptions\Renderer;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class Exception
{





protected $exception;






protected $request;






protected $listener;






protected $basePath;









public function __construct(FlattenException $exception, Request $request, Listener $listener, string $basePath)
{
$this->exception = $exception;
$this->request = $request;
$this->listener = $listener;
$this->basePath = $basePath;
}






public function title()
{
return $this->exception->getStatusText();
}






public function message()
{
return $this->exception->getMessage();
}






public function class()
{
return $this->exception->getClass();
}






public function defaultFrame()
{
$key = array_search(false, array_map(function (Frame $frame) {
return $frame->isFromVendor();
}, $this->frames()->all()));

return $key === false ? 0 : $key;
}






public function frames()
{
$classMap = once(fn () => array_map(function ($path) {
return (string) realpath($path);
}, array_values(ClassLoader::getRegisteredLoaders())[0]->getClassMap()));

$trace = array_values(array_filter(
$this->exception->getTrace(), fn ($trace) => isset($trace['file']),
));

if (($trace[1]['class'] ?? '') === HandleExceptions::class) {
array_shift($trace);
array_shift($trace);
}

return new Collection(array_map(
fn (array $trace) => new Frame($this->exception, $classMap, $trace, $this->basePath), $trace,
));
}






public function request()
{
return $this->request;
}






public function requestHeaders()
{
return array_map(function (array $header) {
return implode(', ', $header);
}, $this->request()->headers->all());
}






public function requestBody()
{
if (empty($payload = $this->request()->all())) {
return null;
}

$json = (string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

return str_replace('\\', '', $json);
}






public function applicationRouteContext()
{
$route = $this->request()->route();

return $route ? array_filter([
'controller' => $route->getActionName(),
'route name' => $route->getName() ?: null,
'middleware' => implode(', ', array_map(function ($middleware) {
return $middleware instanceof Closure ? 'Closure' : $middleware;
}, $route->gatherMiddleware())),
]) : [];
}






public function applicationRouteParametersContext()
{
$parameters = $this->request()->route()?->parameters();

return $parameters ? json_encode(array_map(
fn ($value) => $value instanceof Model ? $value->withoutRelations() : $value,
$parameters
), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null;
}






public function applicationQueries()
{
return array_map(function (array $query) {
$sql = $query['sql'];

foreach ($query['bindings'] as $binding) {
$sql = match (gettype($binding)) {
'integer', 'double' => preg_replace('/\?/', $binding, $sql, 1),
'NULL' => preg_replace('/\?/', 'NULL', $sql, 1),
default => preg_replace('/\?/', "'$binding'", $sql, 1),
};
}

return [
'connectionName' => $query['connectionName'],
'time' => $query['time'],
'sql' => $sql,
];
}, $this->listener->queries());
}
}
