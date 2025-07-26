<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestSize;

/**
@immutable
*/
abstract class Known extends TestSize
{
public function isKnown(): true
{
return true;
}

abstract public function isGreaterThan(self $other): bool;
}
