<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use function call_user_func_array;
use PHPUnit\Framework\MockObject\Invocation;

/**
@no-named-arguments


*/
final class ReturnCallback implements Stub
{



private $callback;

public function __construct(callable $callback)
{
$this->callback = $callback;
}

public function invoke(Invocation $invocation): mixed
{
return call_user_func_array($this->callback, $invocation->parameters());
}
}
