<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class Constant
{
private string $name;
private bool|string $value;

public function __construct(string $name, bool|string $value)
{
$this->name = $name;
$this->value = $value;
}

public function name(): string
{
return $this->name;
}

public function value(): bool|string
{
return $this->value;
}
}
