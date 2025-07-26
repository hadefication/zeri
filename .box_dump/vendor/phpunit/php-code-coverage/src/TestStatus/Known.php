<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestStatus;

/**
@immutable
*/
abstract class Known extends TestStatus
{
public function isKnown(): true
{
return true;
}
}
