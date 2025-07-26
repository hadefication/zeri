<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestSize;

/**
@no-named-arguments
@immutable



*/
final readonly class Small extends Known
{
public function isSmall(): true
{
return true;
}

public function isGreaterThan(TestSize $other): bool
{
return false;
}

public function asString(): string
{
return 'small';
}
}
