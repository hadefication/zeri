<?php









namespace Mockery;

use Mockery;
use Mockery\Exception\NoMatchingExpectationException;

use function array_pop;
use function array_unshift;
use function end;

use const PHP_EOL;

class ExpectationDirector
{





protected $_defaults = [];






protected $_expectations = [];






protected $_expectedOrder = null;






protected $_mock = null;






protected $_name = null;






public function __construct($name, LegacyMockInterface $mock)
{
$this->_name = $name;
$this->_mock = $mock;
}




public function addExpectation(Expectation $expectation)
{
$this->_expectations[] = $expectation;
}






public function call(array $args)
{
$expectation = $this->findExpectation($args);
if ($expectation !== null) {
return $expectation->verifyCall($args);
}

$exception = new NoMatchingExpectationException(
'No matching handler found for '
. $this->_mock->mockery_getName() . '::'
. Mockery::formatArgs($this->_name, $args)
. '. Either the method was unexpected or its arguments matched'
. ' no expected argument list for this method'
. PHP_EOL . PHP_EOL
. Mockery::formatObjects($args)
);

$exception->setMock($this->_mock)
->setMethodName($this->_name)
->setActualArguments($args);

throw $exception;
}






public function findExpectation(array $args)
{
$expectation = null;

if ($this->_expectations !== []) {
$expectation = $this->_findExpectationIn($this->_expectations, $args);
}

if ($expectation === null && $this->_defaults !== []) {
return $this->_findExpectationIn($this->_defaults, $args);
}

return $expectation;
}






public function getDefaultExpectations()
{
return $this->_defaults;
}






public function getExpectationCount()
{
$count = 0;

$expectations = $this->getExpectations();

if ($expectations === []) {
$expectations = $this->getDefaultExpectations();
}

foreach ($expectations as $expectation) {
if ($expectation->isCallCountConstrained()) {
++$count;
}
}

return $count;
}






public function getExpectations()
{
return $this->_expectations;
}








public function makeExpectationDefault(Expectation $expectation)
{
if (end($this->_expectations) === $expectation) {
array_pop($this->_expectations);

array_unshift($this->_defaults, $expectation);

return;
}

throw new Exception('Cannot turn a previously defined expectation into a default');
}








public function verify()
{
if ($this->_expectations !== []) {
foreach ($this->_expectations as $expectation) {
$expectation->verify();
}

return;
}

foreach ($this->_defaults as $expectation) {
$expectation->verify();
}
}








protected function _findExpectationIn(array $expectations, array $args)
{
foreach ($expectations as $expectation) {
if (! $expectation->isEligible()) {
continue;
}

if (! $expectation->matchArgs($args)) {
continue;
}

return $expectation;
}

foreach ($expectations as $expectation) {
if ($expectation->matchArgs($args)) {
return $expectation;
}
}

return null;
}
}
