<?php









namespace Mockery\Exception;

use Mockery\Exception;
use Mockery\LegacyMockInterface;

class InvalidOrderException extends Exception
{



protected $actual = null;




protected $expected = 0;




protected $method = null;




protected $mockObject = null;




public function getActualOrder()
{
return $this->actual;
}




public function getExpectedOrder()
{
return $this->expected;
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






public function setActualOrder($count)
{
$this->actual = $count;
return $this;
}






public function setExpectedOrder($count)
{
$this->expected = $count;
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
