<?php declare(strict_types=1);








namespace SebastianBergmann\ObjectEnumerator;

use function array_merge;
use function is_array;
use function is_object;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SebastianBergmann\RecursionContext\Context;

final class Enumerator
{





public function enumerate(array|object $variable, Context $processed = new Context): array
{
$objects = [];

if ($processed->contains($variable)) {
return $objects;
}

$array = $variable;


$processed->add($variable);

if (is_array($variable)) {
/**
@phpstan-ignore */
foreach ($array as $element) {
if (!is_array($element) && !is_object($element)) {
continue;
}

/**
@noinspection */
$objects = array_merge(
$objects,
$this->enumerate($element, $processed),
);
}

return $objects;
}

$objects[] = $variable;

foreach ((new ObjectReflector)->getProperties($variable) as $value) {
if (!is_array($value) && !is_object($value)) {
continue;
}

/**
@noinspection */
$objects = array_merge(
$objects,
$this->enumerate($value, $processed),
);
}

return $objects;
}
}
