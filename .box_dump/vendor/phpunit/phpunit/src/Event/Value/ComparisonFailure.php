<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

/**
@immutable
@no-named-arguments

*/
final readonly class ComparisonFailure
{
private string $expected;
private string $actual;
private string $diff;

public function __construct(string $expected, string $actual, string $diff)
{
$this->expected = $expected;
$this->actual = $actual;
$this->diff = $diff;
}

public function expected(): string
{
return $this->expected;
}

public function actual(): string
{
return $this->actual;
}

public function diff(): string
{
return $this->diff;
}
}
