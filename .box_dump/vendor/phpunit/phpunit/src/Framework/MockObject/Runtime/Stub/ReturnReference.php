<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;

/**
@no-named-arguments


*/
final class ReturnReference implements Stub
{
private mixed $reference;

public function __construct(mixed &$reference)
{
$this->reference = &$reference;
}

public function invoke(Invocation $invocation): mixed
{
return $this->reference;
}
}
