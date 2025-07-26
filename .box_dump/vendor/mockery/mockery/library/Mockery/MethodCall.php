<?php









namespace Mockery;

class MethodCall
{



private $args;




private $method;





public function __construct($method, $args)
{
$this->method = $method;
$this->args = $args;
}




public function getArgs()
{
return $this->args;
}




public function getMethod()
{
return $this->method;
}
}
