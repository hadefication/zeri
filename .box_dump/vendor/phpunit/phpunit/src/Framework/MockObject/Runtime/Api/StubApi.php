<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments


*/
trait StubApi
{
private readonly TestDoubleState $__phpunit_state;

public function __phpunit_state(): TestDoubleState
{
return $this->__phpunit_state;
}

/**
@noinspection */
public function __phpunit_getInvocationHandler(): InvocationHandler
{
return $this->__phpunit_state()->invocationHandler();
}

/**
@noinspection */
public function __phpunit_unsetInvocationMocker(): void
{
$this->__phpunit_state()->unsetInvocationHandler();
}
}
