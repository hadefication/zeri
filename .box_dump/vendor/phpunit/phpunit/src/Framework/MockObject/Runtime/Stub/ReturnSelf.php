<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\RuntimeException;

/**
@no-named-arguments


*/
final class ReturnSelf implements Stub
{



public function invoke(Invocation $invocation): object
{
return $invocation->object();
}
}
