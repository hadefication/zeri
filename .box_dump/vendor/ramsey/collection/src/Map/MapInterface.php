<?php











declare(strict_types=1);

namespace Ramsey\Collection\Map;

use Ramsey\Collection\ArrayInterface;

/**
@template
@template
@extends




*/
interface MapInterface extends ArrayInterface
{





public function containsKey(int | string $key): bool;








public function containsValue(mixed $value): bool;






public function keys(): array;











public function get(int | string $key, mixed $defaultValue = null): mixed;













public function put(int | string $key, mixed $value): mixed;














public function putIfAbsent(int | string $key, mixed $value): mixed;









public function remove(int | string $key): mixed;












public function removeIf(int | string $key, mixed $value): bool;











public function replace(int | string $key, mixed $value): mixed;













public function replaceIf(int | string $key, mixed $oldValue, mixed $newValue): bool;
}
