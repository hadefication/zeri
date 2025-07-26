<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestSize;

/**
@no-named-arguments
@immutable



*/
final readonly class Large extends Known
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
