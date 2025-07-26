<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestStatus;

/**
@immutable
*/
final class Success extends Known
{
public function isSuccess(): true
{
return true;
}

public function asString(): string
{
return 'success';
}
}
