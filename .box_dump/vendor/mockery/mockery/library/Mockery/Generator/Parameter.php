<?php









namespace Mockery\Generator;

use Mockery\Reflector;
use ReflectionClass;
use ReflectionParameter;
use function class_exists;

/**
@mixin
*/
class Parameter
{



private static $parameterCounter = 0;




private $rfp;

public function __construct(ReflectionParameter $rfp)
{
$this->rfp = $rfp;
}

/**
@template
@template







*/
public function __call($method, array $args)
{

return $this->rfp->{$method}(...$args);
}










public function getClass()
{
$typeHint = Reflector::getTypeHint($this->rfp, true);

return class_exists($typeHint) ? DefinedTargetClass::factory($typeHint, false) : null;
}








public function getName()
{
$name = $this->rfp->getName();

if (! $name || $name === '...') {
return 'arg' . self::$parameterCounter++;
}

return $name;
}






public function getTypeHint()
{
return Reflector::getTypeHint($this->rfp);
}








public function getTypeHintAsString()
{
return (string) Reflector::getTypeHint($this->rfp, true);
}






public function isArray()
{
return Reflector::isArray($this->rfp);
}






public function isVariadic()
{
return $this->rfp->isVariadic();
}
}
