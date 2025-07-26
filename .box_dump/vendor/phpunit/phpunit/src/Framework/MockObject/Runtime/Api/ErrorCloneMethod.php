<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

/**
@no-named-arguments




*/
trait ErrorCloneMethod
{
public function __clone(): void
{
throw new CannotCloneTestDoubleForReadonlyClassException;
}
}
