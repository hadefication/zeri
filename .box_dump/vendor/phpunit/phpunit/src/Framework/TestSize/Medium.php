<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestSize;

/**
@no-named-arguments
@immutable



*/
final readonly class Medium extends Known
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
