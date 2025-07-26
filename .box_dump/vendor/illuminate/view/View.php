<?php

namespace Illuminate\View;

use ArrayAccess;
use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Engine;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\ViewErrorBag;
use Stringable;
use Throwable;

class View implements ArrayAccess, Htmlable, Stringable, ViewContract
{
use Macroable {
__call as macroCall;
}






protected $factory;






protected $engine;






protected $view;






protected $data;






protected $path;










public function __construct(Factory $factory, Engine $engine, $view, $path, $data = [])
{
$this->view = $view;
$this->path = $path;
$this->engine = $engine;
$this->factory = $factory;

$this->data = $data instanceof Arrayable ? $data->toArray() : (array) $data;
}







public function fragment($fragment)
{
return $this->render(function () use ($fragment) {
return $this->factory->getFragment($fragment);
});
}







public function fragments(?array $fragments = null)
{
return is_null($fragments)
? $this->allFragments()
: (new Collection($fragments))->map(fn ($f) => $this->fragment($f))->implode('');
}








public function fragmentIf($boolean, $fragment)
{
if (value($boolean)) {
return $this->fragment($fragment);
}

return $this->render();
}








public function fragmentsIf($boolean, ?array $fragments = null)
{
if (value($boolean)) {
return $this->fragments($fragments);
}

return $this->render();
}






protected function allFragments()
{
return (new Collection($this->render(fn () => $this->factory->getFragments())))->implode('');
}









public function render(?callable $callback = null)
{
try {
$contents = $this->renderContents();

$response = isset($callback) ? $callback($this, $contents) : null;




$this->factory->flushStateIfDoneRendering();

return ! is_null($response) ? $response : $contents;
} catch (Throwable $e) {
$this->factory->flushState();

throw $e;
}
}






protected function renderContents()
{



$this->factory->incrementRender();

$this->factory->callComposer($this);

$contents = $this->getContents();




$this->factory->decrementRender();

return $contents;
}






protected function getContents()
{
return $this->engine->get($this->path, $this->gatherData());
}






public function gatherData()
{
$data = array_merge($this->factory->getShared(), $this->data);

foreach ($data as $key => $value) {
if ($value instanceof Renderable) {
$data[$key] = $value->render();
}
}

return $data;
}








public function renderSections()
{
return $this->render(function () {
return $this->factory->getSections();
});
}








public function with($key, $value = null)
{
if (is_array($key)) {
$this->data = array_merge($this->data, $key);
} else {
$this->data[$key] = $value;
}

return $this;
}









public function nest($key, $view, array $data = [])
{
return $this->with($key, $this->factory->make($view, $data));
}








public function withErrors($provider, $bag = 'default')
{
return $this->with('errors', (new ViewErrorBag)->put(
$bag, $this->formatErrors($provider)
));
}







protected function formatErrors($provider)
{
return $provider instanceof MessageProvider
? $provider->getMessageBag()
: new MessageBag((array) $provider);
}






public function name()
{
return $this->getName();
}






public function getName()
{
return $this->view;
}






public function getData()
{
return $this->data;
}






public function getPath()
{
return $this->path;
}







public function setPath($path)
{
$this->path = $path;
}






public function getFactory()
{
return $this->factory;
}






public function getEngine()
{
return $this->engine;
}







public function offsetExists($key): bool
{
return array_key_exists($key, $this->data);
}







public function offsetGet($key): mixed
{
return $this->data[$key];
}








public function offsetSet($key, $value): void
{
$this->with($key, $value);
}







public function offsetUnset($key): void
{
unset($this->data[$key]);
}







public function &__get($key)
{
return $this->data[$key];
}








public function __set($key, $value)
{
$this->with($key, $value);
}







public function __isset($key)
{
return isset($this->data[$key]);
}







public function __unset($key)
{
unset($this->data[$key]);
}










public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

if (! str_starts_with($method, 'with')) {
throw new BadMethodCallException(sprintf(
'Method %s::%s does not exist.', static::class, $method
));
}

return $this->with(Str::camel(substr($method, 4)), $parameters[0]);
}






public function toHtml()
{
return $this->render();
}








public function __toString()
{
return $this->render();
}
}
