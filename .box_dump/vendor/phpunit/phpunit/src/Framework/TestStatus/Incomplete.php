<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Incomplete extends Known
{
public function isIncomplete(): true
{
return true;
}

public function asInt(): int
{
return 2;
}

public function asString(): string
{
return 'incomplete';
}
}
