<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestSize;

/**
@no-named-arguments
@immutable



*/
final readonly class Unknown extends TestSize
{
public function isUnknown(): true
{
return true;
}

public function asString(): string
{
return 'unknown';
}
}
