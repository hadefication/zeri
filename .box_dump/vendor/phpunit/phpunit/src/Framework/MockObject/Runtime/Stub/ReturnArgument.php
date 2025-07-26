<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;

/**
@no-named-arguments


*/
final readonly class ReturnArgument implements Stub
{
private int $argumentIndex;

public function __construct(int $argumentIndex)
{
$this->argumentIndex = $argumentIndex;
}

public function invoke(Invocation $invocation): mixed
{
return $invocation->parameters()[$this->argumentIndex] ?? null;
}
}
