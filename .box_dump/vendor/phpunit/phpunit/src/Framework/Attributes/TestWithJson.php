<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class TestWithJson
{



private string $json;




private ?string $name;





public function __construct(string $json, ?string $name = null)
{
$this->json = $json;
$this->name = $name;
}




public function json(): string
{
return $this->json;
}




public function name(): ?string
{
return $this->name;
}
}
