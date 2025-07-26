<?php

declare(strict_types=1);

namespace Pest\Concerns;

use Closure;




trait Extendable
{





private static array $extends = [];




public function extend(string $name, Closure $extend): void
{
static::$extends[$name] = $extend;
}




public static function hasExtend(string $name): bool
{
return array_key_exists($name, static::$extends);
}
}
