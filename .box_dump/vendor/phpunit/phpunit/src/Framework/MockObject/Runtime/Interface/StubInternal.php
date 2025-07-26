<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments


*/
interface StubInternal extends Stub
{
public function __phpunit_state(): TestDoubleState;

public function __phpunit_getInvocationHandler(): InvocationHandler;

public function __phpunit_unsetInvocationMocker(): void;

public function __phpunit_wasGeneratedAsMockObject(): bool;
}
