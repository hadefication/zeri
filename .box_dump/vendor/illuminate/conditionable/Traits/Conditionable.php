<?php

namespace Illuminate\Support\Traits;

use Closure;
use Illuminate\Support\HigherOrderWhenProxy;

trait Conditionable
{
/**
@template
@template







*/
public function when($value = null, ?callable $callback = null, ?callable $default = null)
{
$value = $value instanceof Closure ? $value($this) : $value;

if (func_num_args() === 0) {
return new HigherOrderWhenProxy($this);
}

if (func_num_args() === 1) {
return (new HigherOrderWhenProxy($this))->condition($value);
}

if ($value) {
return $callback($this, $value) ?? $this;
} elseif ($default) {
return $default($this, $value) ?? $this;
}

return $this;
}

/**
@template
@template







*/
public function unless($value = null, ?callable $callback = null, ?callable $default = null)
{
$value = $value instanceof Closure ? $value($this) : $value;

if (func_num_args() === 0) {
return (new HigherOrderWhenProxy($this))->negateConditionOnCapture();
}

if (func_num_args() === 1) {
return (new HigherOrderWhenProxy($this))->condition(! $value);
}

if (! $value) {
return $callback($this, $value) ?? $this;
} elseif ($default) {
return $default($this, $value) ?? $this;
}

return $this;
}
}
