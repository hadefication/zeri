<?php

namespace Illuminate\Contracts\Cache;

use Closure;
use Psr\SimpleCache\CacheInterface;

interface Repository extends CacheInterface
{
/**
@template






*/
public function pull($key, $default = null);









public function put($key, $value, $ttl = null);









public function add($key, $value, $ttl = null);








public function increment($key, $value = 1);








public function decrement($key, $value = 1);








public function forever($key, $value);

/**
@template







*/
public function remember($key, $ttl, Closure $callback);

/**
@template






*/
public function sear($key, Closure $callback);

/**
@template






*/
public function rememberForever($key, Closure $callback);







public function forget($key);






public function getStore();
}
