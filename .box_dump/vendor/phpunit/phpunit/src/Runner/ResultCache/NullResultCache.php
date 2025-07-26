<?php declare(strict_types=1);








namespace PHPUnit\Runner\ResultCache;

use PHPUnit\Framework\TestStatus\TestStatus;

/**
@no-named-arguments


*/
final readonly class NullResultCache implements ResultCache
{
public function setStatus(string $id, TestStatus $status): void
{
}

public function status(string $id): TestStatus
{
return TestStatus::unknown();
}

public function setTime(string $id, float $time): void
{
}

public function time(string $id): float
{
return 0;
}

public function load(): void
{
}

public function persist(): void
{
}
}
