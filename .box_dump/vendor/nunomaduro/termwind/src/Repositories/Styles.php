<?php

declare(strict_types=1);

namespace Termwind\Repositories;

use Closure;
use Termwind\ValueObjects\Style;
use Termwind\ValueObjects\Styles as StylesValueObject;




final class Styles
{



private static array $storage = [];






public static function create(string $name, ?Closure $callback = null): Style
{
self::$storage[$name] = $style = new Style(
$callback ?? static fn (StylesValueObject $styles) => $styles
);

return $style;
}




public static function flush(): void
{
self::$storage = [];
}




public static function has(string $name): bool
{
return array_key_exists($name, self::$storage);
}




public static function get(string $name): Style
{
return self::$storage[$name];
}
}
