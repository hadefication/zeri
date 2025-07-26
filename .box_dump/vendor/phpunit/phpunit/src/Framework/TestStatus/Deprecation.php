<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Deprecation extends Known
{
public function isDeprecation(): true
{
return true;
}

public function asInt(): int
{
return 4;
}

public function asString(): string
{
return 'deprecation';
}
}
