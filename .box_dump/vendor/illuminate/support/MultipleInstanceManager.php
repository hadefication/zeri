<?php

namespace Illuminate\Support;

use Closure;
use InvalidArgumentException;
use RuntimeException;

abstract class MultipleInstanceManager
{





protected $app;






protected $config;






protected $instances = [];






protected $customCreators = [];






protected $driverKey = 'driver';






public function __construct($app)
{
$this->app = $app;
$this->config = $app->make('config');
}






abstract public function getDefaultInstance();







abstract public function setDefaultInstance($name);







abstract public function getInstanceConfig($name);







public function instance($name = null)
{
$name = $name ?: $this->getDefaultInstance();

return $this->instances[$name] = $this->get($name);
}







protected function get($name)
{
return $this->instances[$name] ?? $this->resolve($name);
}










protected function resolve($name)
{
$config = $this->getInstanceConfig($name);

if (is_null($config)) {
throw new InvalidArgumentException("Instance [{$name}] is not defined.");
}

if (! array_key_exists($this->driverKey, $config)) {
throw new RuntimeException("Instance [{$name}] does not specify a {$this->driverKey}.");
}

$driverName = $config[$this->driverKey];

if (isset($this->customCreators[$driverName])) {
return $this->callCustomCreator($config);
} else {
$createMethod = 'create'.ucfirst($driverName).ucfirst($this->driverKey);

if (method_exists($this, $createMethod)) {
return $this->{$createMethod}($config);
}

$createMethod = 'create'.Str::studly($driverName).ucfirst($this->driverKey);

if (method_exists($this, $createMethod)) {
return $this->{$createMethod}($config);
}

throw new InvalidArgumentException("Instance {$this->driverKey} [{$config[$this->driverKey]}] is not supported.");
}
}







protected function callCustomCreator(array $config)
{
return $this->customCreators[$config[$this->driverKey]]($this->app, $config);
}







public function forgetInstance($name = null)
{
$name ??= $this->getDefaultInstance();

foreach ((array) $name as $instanceName) {
if (isset($this->instances[$instanceName])) {
unset($this->instances[$instanceName]);
}
}

return $this;
}







public function purge($name = null)
{
$name ??= $this->getDefaultInstance();

unset($this->instances[$name]);
}

/**
@param-closure-this







*/
public function extend($name, Closure $callback)
{
$this->customCreators[$name] = $callback->bindTo($this, $this);

return $this;
}







public function setApplication($app)
{
$this->app = $app;

return $this;
}








public function __call($method, $parameters)
{
return $this->instance()->$method(...$parameters);
}
}
