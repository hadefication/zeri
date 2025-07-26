<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class TestWith
{



private array $data;




private ?string $name;





public function __construct(array $data, ?string $name = null)
{
$this->data = $data;
$this->name = $name;
}




public function data(): array
{
return $this->data;
}




public function name(): ?string
{
return $this->name;
}
}
