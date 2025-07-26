<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function array_key_exists;
use function is_array;
use ArrayAccess;
use PHPUnit\Util\Exporter;

/**
@no-named-arguments
*/
final class ArrayHasKey extends Constraint
{
private readonly mixed $key;

public function __construct(mixed $key)
{
$this->key = $key;
}




public function toString(): string
{
return 'has the key ' . Exporter::export($this->key);
}





protected function matches(mixed $other): bool
{
if (is_array($other)) {
return array_key_exists($this->key, $other);
}

if ($other instanceof ArrayAccess) {
return $other->offsetExists($this->key);
}

return false;
}







protected function failureDescription(mixed $other): string
{
return 'an array ' . $this->toString();
}
}
