<?php

namespace Illuminate\Support;

use Closure;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;

abstract class Manager
{





protected $container;






protected $config;






protected $customCreators = [];






protected $drivers = [];






public function __construct(Container $container)
{
$this->container = $container;
$this->config = $container->make('config');
}






abstract public function getDefaultDriver();









public function driver($driver = null)
{
$driver = $driver ?: $this->getDefaultDriver();

if (is_null($driver)) {
throw new InvalidArgumentException(sprintf(
'Unable to resolve NULL driver for [%s].', static::class
));
}




return $this->drivers[$driver] ??= $this->createDriver($driver);
}









protected function createDriver($driver)
{



if (isset($this->customCreators[$driver])) {
return $this->callCustomCreator($driver);
}

$method = 'create'.Str::studly($driver).'Driver';

if (method_exists($this, $method)) {
return $this->$method();
}

throw new InvalidArgumentException("Driver [$driver] not supported.");
}







protected function callCustomCreator($driver)
{
return $this->customCreators[$driver]($this->container);
}








public function extend($driver, Closure $callback)
{
$this->customCreators[$driver] = $callback;

return $this;
}






public function getDrivers()
{
return $this->drivers;
}






public function getContainer()
{
return $this->container;
}







public function setContainer(Container $container)
{
$this->container = $container;

return $this;
}






public function forgetDrivers()
{
$this->drivers = [];

return $this;
}








public function __call($method, $parameters)
{
return $this->driver()->$method(...$parameters);
}
}
