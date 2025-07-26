<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use PHPUnit\Framework\MockObject\Invocation;

/**
@no-named-arguments


*/
interface Stub
{




public function invoke(Invocation $invocation): mixed;
}
