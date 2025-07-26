<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\TestStatus\TestStatus;

/**
@immutable
@no-named-arguments



*/
final readonly class TestResult
{
private TestMethod $test;
private TestStatus $status;
private ?Throwable $throwable;

public function __construct(TestMethod $test, TestStatus $status, ?Throwable $throwable)
{
$this->test = $test;
$this->status = $status;
$this->throwable = $throwable;
}

public function test(): TestMethod
{
return $this->test;
}

public function status(): TestStatus
{
return $this->status;
}

/**
@phpstan-assert-if-true
*/
public function hasThrowable(): bool
{
return $this->throwable !== null;
}

public function throwable(): ?Throwable
{
return $this->throwable;
}
}
