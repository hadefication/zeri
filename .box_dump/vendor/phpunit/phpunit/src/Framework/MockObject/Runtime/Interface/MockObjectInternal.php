<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments


*/
interface MockObjectInternal extends MockObject, StubInternal
{
public function __phpunit_hasMatchers(): bool;

public function __phpunit_verify(bool $unsetInvocationMocker = true): void;
}
