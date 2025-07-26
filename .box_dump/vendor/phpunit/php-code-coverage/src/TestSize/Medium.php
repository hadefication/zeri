<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestSize;

/**
@immutable
*/
final class Medium extends Known
{
public function isMedium(): true
{
return true;
}

public function isGreaterThan(TestSize $other): bool
{
return $other->isSmall();
}

public function asString(): string
{
return 'medium';
}
}
