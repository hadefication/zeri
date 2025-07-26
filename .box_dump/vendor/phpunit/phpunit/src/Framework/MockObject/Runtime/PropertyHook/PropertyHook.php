<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Runtime;

/**
@no-named-arguments
*/
abstract readonly class PropertyHook
{



private string $propertyName;




public static function get(string $propertyName): PropertyGetHook
{
return new PropertyGetHook($propertyName);
}




public static function set(string $propertyName): PropertySetHook
{
return new PropertySetHook($propertyName);
}




protected function __construct(string $propertyName)
{
$this->propertyName = $propertyName;
}




public function propertyName(): string
{
return $this->propertyName;
}






abstract public function asString(): string;
}
