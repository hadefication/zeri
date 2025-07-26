<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestStatus;

/**
@immutable
*/
final class Unknown extends TestStatus
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
