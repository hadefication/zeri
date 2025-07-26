<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestSize;

/**
@immutable
*/
final class Large extends Known
{
public function isLarge(): true
{
return true;
}

public function isGreaterThan(TestSize $other): bool
{
return !$other->isLarge();
}

public function asString(): string
{
return 'large';
}
}
