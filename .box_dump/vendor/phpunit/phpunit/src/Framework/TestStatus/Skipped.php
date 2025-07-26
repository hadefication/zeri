<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Skipped extends Known
{
public function isSkipped(): true
{
return true;
}

public function asInt(): int
{
return 1;
}

public function asString(): string
{
return 'skipped';
}
}
