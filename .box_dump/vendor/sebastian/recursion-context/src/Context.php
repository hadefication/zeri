<?php declare(strict_types=1);








namespace SebastianBergmann\RecursionContext;

use const PHP_INT_MAX;
use const PHP_INT_MIN;
use function array_key_exists;
use function array_pop;
use function array_slice;
use function count;
use function is_array;
use function random_int;
use function spl_object_id;
use SplObjectStorage;

final class Context
{



private array $arrays = [];




private SplObjectStorage $objects;

public function __construct()
{
$this->objects = new SplObjectStorage;
}




public function __destruct()
{
foreach ($this->arrays as &$array) {
if (is_array($array)) {
array_pop($array);
array_pop($array);
}
}
}

/**
@template
@param-out



*/
public function add(array|object &$value): false|int|string
{
if (is_array($value)) {
return $this->addArray($value);
}

return $this->addObject($value);
}

/**
@template
@param-out



*/
public function contains(array|object &$value): false|int|string
{
if (is_array($value)) {
return $this->containsArray($value);
}

return $this->containsObject($value);
}




private function addArray(array &$array): int
{
$key = $this->containsArray($array);

if ($key !== false) {
return $key;
}

$key = count($this->arrays);
$this->arrays[] = &$array;

if (!array_key_exists(PHP_INT_MAX, $array) && !array_key_exists(PHP_INT_MAX - 1, $array)) {
$array[] = $key;
$array[] = $this->objects;
} else {






do {
/**
@noinspection */
$key = random_int(PHP_INT_MIN, PHP_INT_MAX);
} while (array_key_exists($key, $array));

$array[$key] = $key;

do {
/**
@noinspection */
$key = random_int(PHP_INT_MIN, PHP_INT_MAX);
} while (array_key_exists($key, $array));

$array[$key] = $this->objects;
}

return $key;
}

private function addObject(object $object): int
{
if (!$this->objects->contains($object)) {
$this->objects->attach($object);
}

return spl_object_id($object);
}




private function containsArray(array $array): false|int
{
$end = array_slice($array, -2);

return isset($end[1]) && $end[1] === $this->objects ? $end[0] : false;
}

private function containsObject(object $value): false|int
{
if ($this->objects->contains($value)) {
return spl_object_id($value);
}

return false;
}
}
