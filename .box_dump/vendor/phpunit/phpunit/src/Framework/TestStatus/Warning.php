<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Warning extends Known
{
public function isWarning(): true
{
return true;
}

public function asInt(): int
{
return 6;
}

public function asString(): string
{
return 'warning';
}
}
