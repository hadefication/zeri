<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Code\TestCollection;

/**
@immutable
@no-named-arguments

*/
abstract readonly class TestSuite
{



private string $name;
private int $count;
private TestCollection $tests;




public function __construct(string $name, int $size, TestCollection $tests)
{
$this->name = $name;
$this->count = $size;
$this->tests = $tests;
}




public function name(): string
{
return $this->name;
}

public function count(): int
{
return $this->count;
}

public function tests(): TestCollection
{
return $this->tests;
}

/**
@phpstan-assert-if-true
*/
public function isWithName(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isForTestClass(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isForTestMethodWithDataProvider(): bool
{
return false;
}
}
