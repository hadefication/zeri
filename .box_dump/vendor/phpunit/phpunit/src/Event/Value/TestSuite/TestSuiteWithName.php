<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

/**
@immutable
@no-named-arguments

*/
final readonly class TestSuiteWithName extends TestSuite
{
public function isWithName(): true
{
return true;
}
}
