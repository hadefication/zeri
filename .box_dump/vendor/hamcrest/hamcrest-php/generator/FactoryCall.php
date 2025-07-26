<?php





class FactoryCall
{





const INDENT = '    ';




private $method;




private $name;

public function __construct(FactoryMethod $method, $name)
{
$this->method = $method;
$this->name = $name;
}

public function getMethod()
{
return $this->method;
}

public function getName()
{
return $this->name;
}
}
