<?php









namespace Mockery\Generator;

use Mockery\Reflector;
use ReflectionMethod;
use ReflectionParameter;

use function array_map;

/**
@mixin
*/
class Method
{



private $method;

public function __construct(ReflectionMethod $method)
{
$this->method = $method;
}

/**
@template
@template





*/
public function __call($method, $args)
{

return $this->method->{$method}(...$args);
}




public function getParameters()
{
return array_map(static function (ReflectionParameter $parameter) {
return new Parameter($parameter);
}, $this->method->getParameters());
}




public function getReturnType()
{
return Reflector::getReturnType($this->method);
}
}
