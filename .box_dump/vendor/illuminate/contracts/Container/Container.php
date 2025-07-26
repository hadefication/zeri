<?php

namespace Illuminate\Contracts\Container;

use Closure;
use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
/**
@template





*/
public function get(string $id);







public function bound($abstract);










public function alias($abstract, $alias);








public function tag($abstracts, $tags);







public function tagged($tag);









public function bind($abstract, $concrete = null, $shared = false);








public function bindMethod($method, $callback);









public function bindIf($abstract, $concrete = null, $shared = false);








public function singleton($abstract, $concrete = null);








public function singletonIf($abstract, $concrete = null);








public function scoped($abstract, $concrete = null);








public function scopedIf($abstract, $concrete = null);










public function extend($abstract, Closure $closure);

/**
@template






*/
public function instance($abstract, $instance);









public function addContextualBinding($concrete, $abstract, $implementation);







public function when($concrete);

/**
@template





*/
public function factory($abstract);






public function flush();

/**
@template








*/
public function make($abstract, array $parameters = []);









public function call($callback, array $parameters = [], $defaultMethod = null);







public function resolved($abstract);








public function beforeResolving($abstract, ?Closure $callback = null);








public function resolving($abstract, ?Closure $callback = null);








public function afterResolving($abstract, ?Closure $callback = null);
}
