<?php

namespace Laravel\Prompts\Concerns;

use Closure;
use RuntimeException;

trait Fallback
{



protected static bool $shouldFallback = false;






protected static array $fallbacks = [];




public static function fallbackWhen(bool $condition): void
{
static::$shouldFallback = $condition || static::$shouldFallback;
}




public static function shouldFallback(): bool
{
return static::$shouldFallback && isset(static::$fallbacks[static::class]);
}






public static function fallbackUsing(Closure $fallback): void
{
static::$fallbacks[static::class] = $fallback;
}




public function fallback(): mixed
{
$fallback = static::$fallbacks[static::class] ?? null;

if ($fallback === null) {
throw new RuntimeException('No fallback implementation registered for ['.static::class.']');
}

return $fallback($this);
}
}
