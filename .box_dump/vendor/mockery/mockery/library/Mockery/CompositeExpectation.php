<?php









namespace Mockery;

use function array_map;
use function current;
use function implode;
use function reset;

class CompositeExpectation implements ExpectationInterface
{





protected $_expectations = [];








public function __call($method, array $args)
{
foreach ($this->_expectations as $expectation) {
$expectation->{$method}(...$args);
}

return $this;
}






public function __toString()
{
$parts = array_map(static function (ExpectationInterface $expectation): string {
return (string) $expectation;
}, $this->_expectations);

return '[' . implode(', ', $parts) . ']';
}








public function add($expectation)
{
$this->_expectations[] = $expectation;
}




public function andReturn(...$args)
{
return $this->__call(__FUNCTION__, $args);
}








public function andReturns(...$args)
{
return $this->andReturn(...$args);
}






public function getMock()
{
reset($this->_expectations);
$first = current($this->_expectations);
return $first->getMock();
}






public function getOrderNumber()
{
reset($this->_expectations);
$first = current($this->_expectations);
return $first->getOrderNumber();
}






public function mock()
{
return $this->getMock();
}








public function shouldNotReceive(...$args)
{
reset($this->_expectations);
$first = current($this->_expectations);
return $first->getMock()->shouldNotReceive(...$args);
}








public function shouldReceive(...$args)
{
reset($this->_expectations);
$first = current($this->_expectations);
return $first->getMock()->shouldReceive(...$args);
}
}
