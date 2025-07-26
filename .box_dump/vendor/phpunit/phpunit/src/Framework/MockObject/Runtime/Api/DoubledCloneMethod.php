<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments


*/
trait DoubledCloneMethod
{
public function __clone(): void
{
$this->__phpunit_state = clone $this->__phpunit_state;

$this->__phpunit_state()->cloneInvocationHandler();
}

abstract public function __phpunit_state(): TestDoubleState;
}
