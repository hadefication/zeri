<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function call_user_func_array;
use function func_get_args;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;

/**
@no-named-arguments


*/
trait Method
{
abstract public function __phpunit_getInvocationHandler(): InvocationHandler;

public function method(): InvocationMocker
{
$expects = $this->__phpunit_getInvocationHandler()->expects(new AnyInvokedCount);

return call_user_func_array(
[$expects, 'method'],
func_get_args(),
);
}
}
