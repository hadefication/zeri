<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Closure;
use Illuminate\Foundation\Mix;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Mockery;

trait InteractsWithContainer
{





protected $originalVite;






protected $originalMix;






protected $originalDeferredCallbacksCollection;








protected function swap($abstract, $instance)
{
return $this->instance($abstract, $instance);
}








protected function instance($abstract, $instance)
{
$this->app->instance($abstract, $instance);

return $instance;
}








protected function mock($abstract, ?Closure $mock = null)
{
return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
}








protected function partialMock($abstract, ?Closure $mock = null)
{
return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args()))->makePartial());
}








protected function spy($abstract, ?Closure $mock = null)
{
return $this->instance($abstract, Mockery::spy(...array_filter(func_get_args())));
}







protected function forgetMock($abstract)
{
$this->app->forgetInstance($abstract);

return $this;
}






protected function withoutVite()
{
if ($this->originalVite == null) {
$this->originalVite = app(Vite::class);
}

Facade::clearResolvedInstance(Vite::class);

$this->swap(Vite::class, new class extends Vite
{
public function __invoke($entrypoints, $buildDirectory = null)
{
return new HtmlString('');
}

public function __call($method, $parameters)
{
return '';
}

public function __toString()
{
return '';
}

public function useIntegrityKey($key)
{
return $this;
}

public function useBuildDirectory($path)
{
return $this;
}

public function useHotFile($path)
{
return $this;
}

public function withEntryPoints($entryPoints)
{
return $this;
}

public function useScriptTagAttributes($attributes)
{
return $this;
}

public function useStyleTagAttributes($attributes)
{
return $this;
}

public function usePreloadTagAttributes($attributes)
{
return $this;
}

public function preloadedAssets()
{
return [];
}

public function reactRefresh()
{
return '';
}

public function content($asset, $buildDirectory = null)
{
return '';
}

public function asset($asset, $buildDirectory = null)
{
return '';
}
});

return $this;
}






protected function withVite()
{
if ($this->originalVite) {
$this->app->instance(Vite::class, $this->originalVite);
}

return $this;
}






protected function withoutMix()
{
if ($this->originalMix == null) {
$this->originalMix = app(Mix::class);
}

$this->swap(Mix::class, function () {
return new HtmlString('');
});

return $this;
}






protected function withMix()
{
if ($this->originalMix) {
$this->app->instance(Mix::class, $this->originalMix);
}

return $this;
}






protected function withoutDefer()
{
if ($this->originalDeferredCallbacksCollection == null) {
$this->originalDeferredCallbacksCollection = $this->app->make(DeferredCallbackCollection::class);
}

$this->swap(DeferredCallbackCollection::class, new class extends DeferredCallbackCollection
{
public function offsetSet(mixed $offset, mixed $value): void
{
$value();
}
});

return $this;
}






protected function withDefer()
{
if ($this->originalDeferredCallbacksCollection) {
$this->app->instance(DeferredCallbackCollection::class, $this->originalDeferredCallbacksCollection);
}

return $this;
}
}
