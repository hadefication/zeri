<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestSize;

/**
@immutable
*/
final class Unknown extends TestSize
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
