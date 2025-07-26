<?php declare(strict_types=1);








namespace PHPUnit\Framework\Attributes;

use Attribute;

/**
@immutable
@no-named-arguments

*/
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class RequiresSetting
{



private string $setting;




private string $value;





public function __construct(string $setting, string $value)
{
$this->setting = $setting;
$this->value = $value;
}




public function setting(): string
{
return $this->setting;
}




public function value(): string
{
return $this->value;
}
}
