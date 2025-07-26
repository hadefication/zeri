<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

use Ramsey\Collection\AbstractArray;
use Ramsey\Collection\Exception\InvalidArgumentException;
use Traversable;

use function array_key_exists;
use function array_keys;
use function in_array;
use function var_export;

/**
@template
@template
@extends
@implements



*/
abstract class AbstractMap extends AbstractArray implements MapInterface
{



public function __construct(array $data = [])
{
parent::__construct($data);
}




public function getIterator(): Traversable
{
return parent::getIterator();
}







public function offsetSet(mixed $offset, mixed $value): void
{
if ($offset === null) {
throw new InvalidArgumentException(
'Map elements are key/value pairs; a key must be provided for '
. 'value ' . var_export($value, true),
);
}

$this->data[$offset] = $value;
}

public function containsKey(int | string $key): bool
{
return array_key_exists($key, $this->data);
}

public function containsValue(mixed $value): bool
{
return in_array($value, $this->data, true);
}




public function keys(): array
{

return array_keys($this->data);
}







public function get(int | string $key, mixed $defaultValue = null): mixed
{
return $this[$key] ?? $defaultValue;
}








public function put(int | string $key, mixed $value): mixed
{
$previousValue = $this->get($key);
$this[$key] = $value;

return $previousValue;
}








public function putIfAbsent(int | string $key, mixed $value): mixed
{
$currentValue = $this->get($key);

if ($currentValue === null) {
$this[$key] = $value;
}

return $currentValue;
}







public function remove(int | string $key): mixed
{
$previousValue = $this->get($key);
unset($this[$key]);

return $previousValue;
}

public function removeIf(int | string $key, mixed $value): bool
{
if ($this->get($key) === $value) {
unset($this[$key]);

return true;
}

return false;
}








public function replace(int | string $key, mixed $value): mixed
{
$currentValue = $this->get($key);

if ($this->containsKey($key)) {
$this[$key] = $value;
}

return $currentValue;
}

public function replaceIf(int | string $key, mixed $oldValue, mixed $newValue): bool
{
if ($this->get($key) === $oldValue) {
$this[$key] = $newValue;

return true;
}

return false;
}




public function __serialize(): array
{

return parent::__serialize();
}




public function toArray(): array
{

return parent::toArray();
}
}
