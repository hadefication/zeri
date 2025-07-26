<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Risky extends Known
{
public function isRisky(): true
{
return true;
}

public function asInt(): int
{
return 5;
}

public function asString(): string
{
return 'risky';
}
}
