<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestStatus;

/**
@immutable
*/
final class Failure extends Known
{
public function isFailure(): true
{
return true;
}

public function asString(): string
{
return 'failure';
}
}
