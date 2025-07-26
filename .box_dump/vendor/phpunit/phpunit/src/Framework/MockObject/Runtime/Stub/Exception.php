<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;
use Throwable;

/**
@no-named-arguments


*/
final readonly class Exception implements Stub
{
private Throwable $exception;

public function __construct(Throwable $exception)
{
$this->exception = $exception;
}




public function invoke(Invocation $invocation): never
{
throw $this->exception;
}
}
