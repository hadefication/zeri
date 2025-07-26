<?php









namespace Mockery\Exception;

use Mockery\Exception;
use Mockery\LegacyMockInterface;

class NoMatchingExpectationException extends Exception
{



protected $actual = [];




protected $method = null;




protected $mockObject = null;




public function getActualArguments()
{
return $this->actual;
}




public function getMethodName()
{
return $this->method;
}




public function getMock()
{
return $this->mockObject;
}




public function getMockName()
{
$mock = $this->getMock();

if ($mock === null) {
return $mock;
}

return $mock->mockery_getName();
}

/**
@template




*/
public function setActualArguments($count)
{
$this->actual = $count;
return $this;
}





public function setMethodName($name)
{
$this->method = $name;
return $this;
}




public function setMock(LegacyMockInterface $mock)
{
$this->mockObject = $mock;
return $this;
}
}
