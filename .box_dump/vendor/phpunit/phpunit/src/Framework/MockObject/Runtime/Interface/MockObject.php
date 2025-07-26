<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

/**
@no-named-arguments


*/
interface MockObject extends Stub
{
public function expects(InvocationOrder $invocationRule): InvocationMocker;
}
