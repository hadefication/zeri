<?php









namespace Mockery\Generator;

use function array_pop;
use function explode;
use function implode;
use function ltrim;

class UndefinedTargetClass implements TargetClassInterface
{



private $name;




public function __construct($name)
{
$this->name = $name;
}




public function __toString()
{
return $this->name;
}





public static function factory($name)
{
return new self($name);
}




public function getAttributes()
{
return [];
}




public function getInterfaces()
{
return [];
}




public function getMethods()
{
return [];
}




public function getName()
{
return $this->name;
}




public function getNamespaceName()
{
$parts = explode('\\', ltrim($this->getName(), '\\'));
array_pop($parts);
return implode('\\', $parts);
}




public function getShortName()
{
$parts = explode('\\', $this->getName());
return array_pop($parts);
}




public function hasInternalAncestor()
{
return false;
}





public function implementsInterface($interface)
{
return false;
}




public function inNamespace()
{
return $this->getNamespaceName() !== '';
}




public function isAbstract()
{
return false;
}




public function isFinal()
{
return false;
}
}
