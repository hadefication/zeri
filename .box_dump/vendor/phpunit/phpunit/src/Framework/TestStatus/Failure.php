<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Failure extends Known
{
public function isFailure(): true
{
return true;
}

public function asInt(): int
{
return 7;
}

public function asString(): string
{
return 'failure';
}
}
