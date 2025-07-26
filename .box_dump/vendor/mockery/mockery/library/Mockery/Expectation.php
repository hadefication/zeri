<?php









namespace Mockery;

use Closure;
use Hamcrest\Matcher;
use Hamcrest_Matcher;
use InvalidArgumentException;
use Mockery;
use Mockery\CountValidator\AtLeast;
use Mockery\CountValidator\AtMost;
use Mockery\CountValidator\Exact;
use Mockery\Matcher\AndAnyOtherArgs;
use Mockery\Matcher\AnyArgs;
use Mockery\Matcher\ArgumentListMatcher;
use Mockery\Matcher\MatcherInterface;
use Mockery\Matcher\MultiArgumentClosure;
use Mockery\Matcher\NoArgs;
use OutOfBoundsException;
use PHPUnit\Framework\Constraint\Constraint;
use Throwable;

use function array_key_exists;
use function array_search;
use function array_shift;
use function array_slice;
use function count;
use function current;
use function func_get_args;
use function get_class;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function sprintf;
use function trigger_error;

use const E_USER_DEPRECATED;

class Expectation implements ExpectationInterface
{
public const ERROR_ZERO_INVOCATION = 'shouldNotReceive(), never(), times(0) chaining additional invocation count methods has been deprecated and will throw an exception in a future version of Mockery';






protected $_actualCount = 0;






protected $_because = null;







protected $_closureQueue = [];






protected $_countValidatorClass = Exact::class;






protected $_countValidators = [];






protected $_expectedArgs = [];






protected $_expectedCount = -1;







protected $_globally = false;






protected $_globalOrderNumber = null;






protected $_mock = null;






protected $_name = null;






protected $_orderNumber = null;







protected $_passthru = false;






protected $_returnQueue = [];






protected $_returnValue = null;






protected $_setQueue = [];






protected $_throw = false;






public function __construct(LegacyMockInterface $mock, $name)
{
$this->_mock = $mock;
$this->_name = $name;
$this->withAnyArgs();
}




public function __clone()
{
$newValidators = [];

$countValidators = $this->_countValidators;

foreach ($countValidators as $validator) {
$newValidators[] = clone $validator;
}

$this->_countValidators = $newValidators;
}






public function __toString()
{
return Mockery::formatArgs($this->_name, $this->_expectedArgs);
}








public function andReturn(...$args)
{
$this->_returnQueue = $args;

return $this;
}








public function andReturnArg($index)
{
if (! is_int($index) || $index < 0) {
throw new InvalidArgumentException(
'Invalid argument index supplied. Index must be a non-negative integer.'
);
}

$closure = static function (...$args) use ($index) {
if (array_key_exists($index, $args)) {
return $args[$index];
}

throw new OutOfBoundsException(
'Cannot return an argument value. No argument exists for the index ' . $index
);
};

$this->_closureQueue = [$closure];

return $this;
}




public function andReturnFalse()
{
return $this->andReturn(false);
}






public function andReturnNull()
{
return $this->andReturn(null);
}








public function andReturns(...$args)
{
return $this->andReturn(...$args);
}






public function andReturnSelf()
{
return $this->andReturn($this->_mock);
}




public function andReturnTrue()
{
return $this->andReturn(true);
}






public function andReturnUndefined()
{
return $this->andReturn(new Undefined());
}










public function andReturnUsing(...$args)
{
$this->_closureQueue = $args;

return $this;
}






public function andReturnValues(array $values)
{
return $this->andReturn(...$values);
}









public function andSet($name, ...$values)
{
$this->_setQueue[$name] = $values;

return $this;
}










public function andThrow($exception, $message = '', $code = 0, ?\Exception $previous = null)
{
$this->_throw = true;

if (is_object($exception)) {
return $this->andReturn($exception);
}

return $this->andReturn(new $exception($message, $code, $previous));
}






public function andThrowExceptions(array $exceptions)
{
$this->_throw = true;

foreach ($exceptions as $exception) {
if (! is_object($exception)) {
throw new Exception('You must pass an array of exception objects to andThrowExceptions');
}
}

return $this->andReturnValues($exceptions);
}

public function andThrows($exception, $message = '', $code = 0, ?\Exception $previous = null)
{
return $this->andThrow($exception, $message, $code, $previous);
}








public function andYield(...$args)
{
$closure = static function () use ($args) {
foreach ($args as $arg) {
yield $arg;
}
};

$this->_closureQueue = [$closure];

return $this;
}






public function atLeast()
{
$this->_countValidatorClass = AtLeast::class;

return $this;
}






public function atMost()
{
$this->_countValidatorClass = AtMost::class;

return $this;
}








public function because($message)
{
$this->_because = $message;

return $this;
}







public function between($minimum, $maximum)
{
return $this->atLeast()->times($minimum)->atMost()->times($maximum);
}






public function byDefault()
{
$director = $this->_mock->mockery_getExpectationsFor($this->_name);

if ($director instanceof ExpectationDirector) {
$director->makeExpectationDefault($this);
}

return $this;
}




public function getExceptionMessage()
{
return $this->_because;
}






public function getMock()
{
return $this->_mock;
}

public function getName()
{
return $this->_name;
}






public function getOrderNumber()
{
return $this->_orderNumber;
}






public function globally()
{
$this->_globally = true;

return $this;
}






public function isCallCountConstrained()
{
return $this->_countValidators !== [];
}






public function isEligible()
{
foreach ($this->_countValidators as $validator) {
if (! $validator->isEligible($this->_actualCount)) {
return false;
}
}

return true;
}






public function matchArgs(array $args)
{
if ($this->isArgumentListMatcher()) {
return $this->_matchArg($this->_expectedArgs[0], $args);
}

$argCount = count($args);

$expectedArgsCount = count($this->_expectedArgs);

if ($argCount === $expectedArgsCount) {
return $this->_matchArgs($args);
}

$lastExpectedArgument = $this->_expectedArgs[$expectedArgsCount - 1];

if ($lastExpectedArgument instanceof AndAnyOtherArgs) {
$firstCorrespondingKey = array_search($lastExpectedArgument, $this->_expectedArgs, true);

$args = array_slice($args, 0, $firstCorrespondingKey);

return $this->_matchArgs($args);
}

return false;
}






public function never()
{
return $this->times(0);
}






public function once()
{
return $this->times(1);
}








public function ordered($group = null)
{
if ($this->_globally) {
$this->_globalOrderNumber = $this->_defineOrdered($group, $this->_mock->mockery_getContainer());
} else {
$this->_orderNumber = $this->_defineOrdered($group, $this->_mock);
}

$this->_globally = false;

return $this;
}







public function passthru()
{
if ($this->_mock instanceof Mock) {
throw new Exception(
'Mock Objects not created from a loaded/existing class are incapable of passing method calls through to a parent class'
);
}

$this->_passthru = true;

return $this;
}










public function set($name, $value)
{
return $this->andSet(...func_get_args());
}










public function times($limit = null)
{
if ($limit === null) {
return $this;
}

if (! is_int($limit)) {
throw new InvalidArgumentException('The passed Times limit should be an integer value');
}

if ($this->_expectedCount === 0) {
@trigger_error(self::ERROR_ZERO_INVOCATION, E_USER_DEPRECATED);

}

if ($limit === 0) {
$this->_countValidators = [];
}

$this->_expectedCount = $limit;

$this->_countValidators[$this->_countValidatorClass] = new $this->_countValidatorClass($this, $limit);

if ($this->_countValidatorClass !== Exact::class) {
$this->_countValidatorClass = Exact::class;

unset($this->_countValidators[$this->_countValidatorClass]);
}

return $this;
}






public function twice()
{
return $this->times(2);
}






public function validateOrder()
{
if ($this->_orderNumber) {
$this->_mock->mockery_validateOrder((string) $this, $this->_orderNumber, $this->_mock);
}

if ($this->_globalOrderNumber) {
$this->_mock->mockery_getContainer()->mockery_validateOrder(
(string) $this,
$this->_globalOrderNumber,
$this->_mock
);
}
}






public function verify()
{
foreach ($this->_countValidators as $validator) {
$validator->validate($this->_actualCount);
}
}









public function verifyCall(array $args)
{
$this->validateOrder();

++$this->_actualCount;

if ($this->_passthru === true) {
return $this->_mock->mockery_callSubjectMethod($this->_name, $args);
}

$return = $this->_getReturnValue($args);

$this->throwAsNecessary($return);

$this->_setValues();

return $return;
}








public function with(...$args)
{
return $this->withArgs($args);
}






public function withAnyArgs()
{
$this->_expectedArgs = [new AnyArgs()];

return $this;
}









public function withArgs($argsOrClosure)
{
if (is_array($argsOrClosure)) {
return $this->withArgsInArray($argsOrClosure);
}

if ($argsOrClosure instanceof Closure) {
return $this->withArgsMatchedByClosure($argsOrClosure);
}

throw new InvalidArgumentException(sprintf(
'Call to %s with an invalid argument (%s), only array and closure are allowed',
__METHOD__,
$argsOrClosure
));
}






public function withNoArgs()
{
$this->_expectedArgs = [new NoArgs()];

return $this;
}








public function withSomeOfArgs(...$expectedArgs)
{
return $this->withArgs(static function (...$args) use ($expectedArgs): bool {
foreach ($expectedArgs as $expectedArg) {
if (! in_array($expectedArg, $args, true)) {
return false;
}
}

return true;
});
}






public function zeroOrMoreTimes()
{
return $this->atLeast()->never();
}









protected function _defineOrdered($group, $ordering)
{
$groups = $ordering->mockery_getGroups();
if ($group === null) {
return $ordering->mockery_allocateOrder();
}

if (array_key_exists($group, $groups)) {
return $groups[$group];
}

$result = $ordering->mockery_allocateOrder();

$ordering->mockery_setGroup($group, $result);

return $result;
}






protected function _getReturnValue(array $args)
{
$closureQueueCount = count($this->_closureQueue);

if ($closureQueueCount > 1) {
return array_shift($this->_closureQueue)(...$args);
}

if ($closureQueueCount > 0) {
return current($this->_closureQueue)(...$args);
}

$returnQueueCount = count($this->_returnQueue);

if ($returnQueueCount > 1) {
return array_shift($this->_returnQueue);
}

if ($returnQueueCount > 0) {
return current($this->_returnQueue);
}

return $this->_mock->mockery_returnValueForMethod($this->_name);
}









protected function _matchArg($expected, &$actual)
{
if ($expected === $actual) {
return true;
}

if ($expected instanceof MatcherInterface) {
return $expected->match($actual);
}

if ($expected instanceof Constraint) {
return (bool) $expected->evaluate($actual, '', true);
}

if ($expected instanceof Matcher || $expected instanceof Hamcrest_Matcher) {
@trigger_error('Hamcrest package has been deprecated and will be removed in 2.0', E_USER_DEPRECATED);

return $expected->matches($actual);
}

if (is_object($expected)) {
$matcher = Mockery::getConfiguration()->getDefaultMatcher(get_class($expected));

return $matcher === null ? false : $this->_matchArg(new $matcher($expected), $actual);
}

if (is_object($actual) && is_string($expected) && $actual instanceof $expected) {
return true;
}

return $expected == $actual;
}








protected function _matchArgs($args)
{
for ($index = 0, $argCount = count($args); $index < $argCount; ++$index) {
$param = &$args[$index];

if (! $this->_matchArg($this->_expectedArgs[$index], $param)) {
return false;
}
}

return true;
}






protected function _setValues()
{
$mockClass = get_class($this->_mock);

$container = $this->_mock->mockery_getContainer();

$mocks = $container->getMocks();

foreach ($this->_setQueue as $name => &$values) {
if ($values === []) {
continue;
}

$value = array_shift($values);

$this->_mock->{$name} = $value;

foreach ($mocks as $mock) {
if (! $mock instanceof $mockClass) {
continue;
}

if (! $mock->mockery_isInstance()) {
continue;
}

$mock->{$name} = $value;
}
}
}

/**
@template




*/
private function isAndAnyOtherArgumentsMatcher($expectedArg)
{
return $expectedArg instanceof AndAnyOtherArgs;
}






private function isArgumentListMatcher()
{
return $this->_expectedArgs !== [] && $this->_expectedArgs[0] instanceof ArgumentListMatcher;
}










private function throwAsNecessary($return)
{
if (! $this->_throw) {
return;
}

if (! $return instanceof Throwable) {
return;
}

throw $return;
}






private function withArgsInArray(array $arguments)
{
if ($arguments === []) {
return $this->withNoArgs();
}

$this->_expectedArgs = $arguments;

return $this;
}






private function withArgsMatchedByClosure(Closure $closure)
{
$this->_expectedArgs = [new MultiArgumentClosure($closure)];

return $this;
}
}
