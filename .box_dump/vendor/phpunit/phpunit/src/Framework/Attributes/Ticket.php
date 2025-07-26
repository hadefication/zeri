<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Ticket
{



private string $text;




public function __construct(string $text)
{
$this->text = $text;
}




public function text(): string
{
return $this->text;
}
}
