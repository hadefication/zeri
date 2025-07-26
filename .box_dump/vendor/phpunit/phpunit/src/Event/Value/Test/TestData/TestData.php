<?php declare(strict_types=1);








namespace PHPUnit\Event\TestData;

/**
@immutable
@no-named-arguments

*/
abstract readonly class TestData
{
private string $data;

protected function __construct(string $data)
{
$this->data = $data;
}

public function data(): string
{
return $this->data;
}

/**
@phpstan-assert-if-true
*/
public function isFromDataProvider(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isFromTestDependency(): bool
{
return false;
}
}
