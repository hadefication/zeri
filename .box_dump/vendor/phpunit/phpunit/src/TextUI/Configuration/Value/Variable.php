<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class Variable
{
private string $name;
private mixed $value;
private bool $force;

public function __construct(string $name, mixed $value, bool $force)
{
$this->name = $name;
$this->value = $value;
$this->force = $force;
}

public function name(): string
{
return $this->name;
}

public function value(): mixed
{
return $this->value;
}

public function force(): bool
{
return $this->force;
}
}
