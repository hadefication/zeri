<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Success extends Known
{
public function isSuccess(): true
{
return true;
}

public function asInt(): int
{
return 0;
}

public function asString(): string
{
return 'success';
}
}
