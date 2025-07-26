<?php declare(strict_types=1);








namespace PHPUnit\Event\TestData;

/**
@immutable
@no-named-arguments

*/
final readonly class DataFromTestDependency extends TestData
{
public static function from(string $data): self
{
return new self($data);
}

public function isFromTestDependency(): true
{
return true;
}
}
