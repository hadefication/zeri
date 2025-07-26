<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use SebastianBergmann\Type\Type;

/**
@no-named-arguments


*/
final readonly class HookedProperty
{



private string $name;
private Type $type;
private bool $getHook;
private bool $setHook;




public function __construct(string $name, Type $type, bool $getHook, bool $setHook)
{
$this->name = $name;
$this->type = $type;
$this->getHook = $getHook;
$this->setHook = $setHook;
}

public function name(): string
{
return $this->name;
}

public function type(): Type
{
return $this->type;
}

public function hasGetHook(): bool
{
return $this->getHook;
}

public function hasSetHook(): bool
{
return $this->setHook;
}
}
