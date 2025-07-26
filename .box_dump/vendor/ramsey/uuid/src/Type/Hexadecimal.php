<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Type;

use Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;

use function preg_match;
use function sprintf;
use function substr;

/**
@immutable





*/
final class Hexadecimal implements TypeInterface
{



private string $value;




public function __construct(self | string $value)
{
$this->value = $value instanceof self ? (string) $value : $this->prepareValue($value);
}

/**
@pure


*/
public function toString(): string
{
return $this->value;
}




public function __toString(): string
{
return $this->toString();
}




public function jsonSerialize(): string
{
return $this->toString();
}




public function serialize(): string
{
return $this->toString();
}




public function __serialize(): array
{
return ['string' => $this->toString()];
}






public function unserialize(string $data): void
{
$this->__construct($data);
}




public function __unserialize(array $data): void
{

if (!isset($data['string'])) {
throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
}


$this->unserialize($data['string']);
}




private function prepareValue(string $value): string
{
$value = strtolower($value);

if (str_starts_with($value, '0x')) {
$value = substr($value, 2);
}

if (!preg_match('/^[A-Fa-f0-9]+$/', $value)) {
throw new InvalidArgumentException('Value must be a hexadecimal number');
}


return $value;
}
}
