<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class TestWith extends Metadata
{



private array $data;




private ?string $name;






protected function __construct(int $level, array $data, ?string $name = null)
{
parent::__construct($level);

$this->data = $data;
$this->name = $name;
}

public function isTestWith(): true
{
return true;
}




public function data(): array
{
return $this->data;
}

/**
@phpstan-assert-if-true
*/
public function hasName(): bool
{
return $this->name !== null;
}




public function name(): ?string
{
return $this->name;
}
}
