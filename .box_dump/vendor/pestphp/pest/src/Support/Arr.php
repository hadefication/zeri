<?php

declare(strict_types=1);

namespace Pest\Support;




final class Arr
{





public static function has(array $array, string|int $key): bool
{
$key = (string) $key;

if (array_key_exists($key, $array)) {
return true;
}

foreach (explode('.', $key) as $segment) {
if (is_array($array) && array_key_exists($segment, $array)) {
$array = $array[$segment];
} else {
return false;
}
}

return true;
}






public static function get(array $array, string|int $key, mixed $default = null): mixed
{
$key = (string) $key;

if (array_key_exists($key, $array)) {
return $array[$key];
}

if (! str_contains($key, '.')) {
return $array[$key] ?? $default;
}

foreach (explode('.', $key) as $segment) {
if (is_array($array) && array_key_exists($segment, $array)) {
$array = $array[$segment];
} else {
return $default;
}
}

return $array;
}







public static function dot(array $array, string $prepend = ''): array
{
$results = [];

foreach ($array as $key => $value) {
if (is_array($value) && $value !== []) {
$results = array_merge($results, self::dot($value, $prepend.$key.'.'));
} else {
$results[$prepend.$value] = $value;
}
}

return $results;
}






public static function last(array $array): mixed
{
return end($array);
}
}
