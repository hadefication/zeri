<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestSize;

/**
@no-named-arguments
@immutable



*/
abstract readonly class Known extends TestSize
{
public function isKnown(): true
{
return true;
}

abstract public function isGreaterThan(self $other): bool;
}
