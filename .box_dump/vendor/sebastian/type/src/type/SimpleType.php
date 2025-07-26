<?php declare(strict_types=1);








namespace SebastianBergmann\Type;

use function strtolower;

/**
@no-named-arguments
*/
final class SimpleType extends Type
{



private string $name;
private bool $allowsNull;
private mixed $value;




public function __construct(string $name, bool $nullable, mixed $value = null)
{
$this->name = $this->normalize($name);
$this->allowsNull = $nullable;
$this->value = $value;
}

public function isAssignable(Type $other): bool
{
if ($this->allowsNull && $other instanceof NullType) {
return true;
}

if ($this->name === 'bool' && $other->name() === 'true') {
return true;
}

if ($this->name === 'bool' && $other->name() === 'false') {
return true;
}

if ($other instanceof self) {
return $this->name === $other->name;
}

return false;
}




public function name(): string
{
return $this->name;
}

public function allowsNull(): bool
{
return $this->allowsNull;
}

public function value(): mixed
{
return $this->value;
}

public function isSimple(): bool
{
return true;
}






private function normalize(string $name): string
{
$name = strtolower($name);

return match ($name) {
'boolean' => 'bool',
'real', 'double' => 'float',
'integer' => 'int',
'[]' => 'array',
default => $name,
};
}
}
