<?php

namespace Laravel\Prompts\Support;

use Closure;




class Utils
{





public static function allMatch(array $values, Closure $callback): bool
{
foreach ($values as $key => $value) {
if (! $callback($value, $key)) {
return false;
}
}

return true;
}






public static function last(array $array): mixed
{
return array_reverse($array)[0] ?? null;
}






public static function search(array $array, Closure $callback): int|string|false
{
foreach ($array as $key => $value) {
if ($callback($value, $key)) {
return $key;
}
}

return false;
}
}
