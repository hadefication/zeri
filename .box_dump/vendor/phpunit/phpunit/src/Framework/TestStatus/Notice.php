<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class Notice extends Known
{
public function isNotice(): true
{
return true;
}

public function asInt(): int
{
return 3;
}

public function asString(): string
{
return 'notice';
}
}
