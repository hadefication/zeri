<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Error extends Known
{
public function isError(): true
{
return true;
}

public function asInt(): int
{
return 8;
}

public function asString(): string
{
return 'error';
}
}
