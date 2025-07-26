<?php









namespace Mockery;

use Mockery\Container;
use Mockery\CountValidator\Exception;
use Mockery\Exception\BadMethodCallException;
use Mockery\Exception\InvalidOrderException;
use Mockery\Exception\NoMatchingExpectationException;
use Mockery\Expectation;
use Mockery\ExpectationDirector;
use Mockery\ExpectsHigherOrderMessage;
use Mockery\HigherOrderMessage;
use Mockery\LegacyMockInterface;
use Mockery\MethodCall;
use Mockery\MockInterface;
use Mockery\ReceivedMethodCalls;
use Mockery\Reflector;
use Mockery\Undefined;
use Mockery\VerificationDirector;
use Mockery\VerificationExpectation;

#[\AllowDynamicProperties]
class Mock implements MockInterface
{





protected $_mockery_expectations = [];







protected $_mockery_expectations_count = 0;







protected $_mockery_ignoreMissing = false;







protected $_mockery_ignoreMissingRecursive = false;







protected $_mockery_deferMissing = false;






protected $_mockery_verified = false;






protected $_mockery_name = null;






protected $_mockery_allocatedOrder = 0;






protected $_mockery_currentOrder = 0;






protected $_mockery_groups = [];






protected $_mockery_container = null;








protected $_mockery_partial = null;








protected $_mockery_disableExpectationMatching = false;







protected $_mockery_mockableProperties = [];




protected $_mockery_mockableMethods = [];






protected static $_mockery_methods;

protected $_mockery_allowMockingProtectedMethods = false;

protected $_mockery_receivedMethodCalls;





protected $_mockery_defaultReturnValue = null;






protected $_mockery_thrownExceptions = [];

protected $_mockery_instanceMock = true;


private $_mockery_parentClass = null;










public function mockery_init(?Container $container = null, $partialObject = null, $instanceMock = true)
{
if (null === $container) {
$container = new Container();
}

$this->_mockery_container = $container;
if (!is_null($partialObject)) {
$this->_mockery_partial = $partialObject;
}

if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
foreach ($this->mockery_getMethods() as $method) {
if ($method->isPublic()) {
$this->_mockery_mockableMethods[] = $method->getName();
}
}
}

$this->_mockery_instanceMock = $instanceMock;

$this->_mockery_parentClass = get_parent_class($this);
}








public function shouldReceive(...$methodNames)
{
if ($methodNames === []) {
return new HigherOrderMessage($this, 'shouldReceive');
}

foreach ($methodNames as $method) {
if ('' === $method) {
throw new \InvalidArgumentException('Received empty method name');
}
}

$self = $this;
$allowMockingProtectedMethods = $this->_mockery_allowMockingProtectedMethods;
return \Mockery::parseShouldReturnArgs(
$this,
$methodNames,
static function ($method) use ($self, $allowMockingProtectedMethods) {
$rm = $self->mockery_getMethod($method);
if ($rm) {
if ($rm->isPrivate()) {
throw new \InvalidArgumentException($method . '() cannot be mocked as it is a private method');
}

if (!$allowMockingProtectedMethods && $rm->isProtected()) {
throw new \InvalidArgumentException($method . '() cannot be mocked as it is a protected method and mocking protected methods is not enabled for the currently used mock object. Use shouldAllowMockingProtectedMethods() to enable mocking of protected methods.');
}
}

$director = $self->mockery_getExpectationsFor($method);
if (!$director) {
$director = new ExpectationDirector($method, $self);
$self->mockery_setExpectationsFor($method, $director);
}

$expectation = new Expectation($self, $method);
$director->addExpectation($expectation);
return $expectation;
}
);
}






public function allows($something = [])
{
if (is_string($something)) {
return $this->shouldReceive($something);
}

if (empty($something)) {
return $this->shouldReceive();
}

foreach ($something as $method => $returnValue) {
$this->shouldReceive($method)->andReturn($returnValue);
}

return $this;
}








public function expects($something = null)
{
if (is_string($something)) {
return $this->shouldReceive($something)->once();
}

return new ExpectsHigherOrderMessage($this);
}








public function shouldNotReceive(...$methodNames)
{
if ($methodNames === []) {
return new HigherOrderMessage($this, 'shouldNotReceive');
}

$expectation = call_user_func_array(function (string $methodNames) {
return $this->shouldReceive($methodNames);
}, $methodNames);
$expectation->never();
return $expectation;
}







public function shouldAllowMockingMethod($method)
{
$this->_mockery_mockableMethods[] = $method;
return $this;
}







public function shouldIgnoreMissing($returnValue = null, $recursive = false)
{
$this->_mockery_ignoreMissing = true;
$this->_mockery_ignoreMissingRecursive = $recursive;
$this->_mockery_defaultReturnValue = $returnValue;
return $this;
}

public function asUndefined()
{
$this->_mockery_ignoreMissing = true;
$this->_mockery_defaultReturnValue = new Undefined();
return $this;
}




public function shouldAllowMockingProtectedMethods()
{
if (!\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed()) {
foreach ($this->mockery_getMethods() as $method) {
if ($method->isProtected()) {
$this->_mockery_mockableMethods[] = $method->getName();
}
}
}

$this->_mockery_allowMockingProtectedMethods = true;
return $this;
}












public function shouldDeferMissing()
{
return $this->makePartial();
}









public function makePartial()
{
$this->_mockery_deferMissing = true;
return $this;
}








public function byDefault()
{
foreach ($this->_mockery_expectations as $director) {
$exps = $director->getExpectations();
foreach ($exps as $exp) {
$exp->byDefault();
}
}

return $this;
}




public function __call($method, array $args)
{
return $this->_mockery_handleMethodCall($method, $args);
}

public static function __callStatic($method, array $args)
{
return self::_mockery_handleStaticMethodCall($method, $args);
}




#[\ReturnTypeWillChange]
public function __toString()
{
return $this->__call('__toString', []);
}







public function mockery_verify()
{
if ($this->_mockery_verified) {
return;
}

if (property_exists($this, '_mockery_ignoreVerification') && $this->_mockery_ignoreVerification !== null
&& $this->_mockery_ignoreVerification == true) {
return;
}

$this->_mockery_verified = true;
foreach ($this->_mockery_expectations as $director) {
$director->verify();
}
}






public function mockery_thrownExceptions()
{
return $this->_mockery_thrownExceptions;
}






public function mockery_teardown()
{
}






public function mockery_allocateOrder()
{
++$this->_mockery_allocatedOrder;
return $this->_mockery_allocatedOrder;
}







public function mockery_setGroup($group, $order)
{
$this->_mockery_groups[$group] = $order;
}






public function mockery_getGroups()
{
return $this->_mockery_groups;
}






public function mockery_setCurrentOrder($order)
{
$this->_mockery_currentOrder = $order;
return $this->_mockery_currentOrder;
}






public function mockery_getCurrentOrder()
{
return $this->_mockery_currentOrder;
}









public function mockery_validateOrder($method, $order)
{
if ($order < $this->_mockery_currentOrder) {
$exception = new InvalidOrderException(
'Method ' . self::class . '::' . $method . '()'
. ' called out of order: expected order '
. $order . ', was ' . $this->_mockery_currentOrder
);
$exception->setMock($this)
->setMethodName($method)
->setExpectedOrder($order)
->setActualOrder($this->_mockery_currentOrder);
throw $exception;
}

$this->mockery_setCurrentOrder($order);
}






public function mockery_getExpectationCount()
{
$count = $this->_mockery_expectations_count;
foreach ($this->_mockery_expectations as $director) {
$count += $director->getExpectationCount();
}

return $count;
}







public function mockery_setExpectationsFor($method, ExpectationDirector $director)
{
$this->_mockery_expectations[$method] = $director;
}







public function mockery_getExpectationsFor($method)
{
if (isset($this->_mockery_expectations[$method])) {
return $this->_mockery_expectations[$method];
}
}








public function mockery_findExpectation($method, array $args)
{
if (!isset($this->_mockery_expectations[$method])) {
return null;
}

$director = $this->_mockery_expectations[$method];

return $director->findExpectation($args);
}






public function mockery_getContainer()
{
return $this->_mockery_container;
}






public function mockery_getName()
{
return self::class;
}




public function mockery_getMockableProperties()
{
return $this->_mockery_mockableProperties;
}

public function __isset($name)
{
if (false !== stripos($name, '_mockery_')) {
return false;
}

if (!$this->_mockery_parentClass) {
return false;
}

if (!method_exists($this->_mockery_parentClass, '__isset')) {
return false;
}

return call_user_func($this->_mockery_parentClass . '::__isset', $name);
}

public function mockery_getExpectations()
{
return $this->_mockery_expectations;
}










public function mockery_callSubjectMethod($name, array $args)
{
if (!method_exists($this, $name) && $this->_mockery_parentClass && method_exists($this->_mockery_parentClass, '__call')) {
return call_user_func($this->_mockery_parentClass . '::__call', $name, $args);
}

return call_user_func_array($this->_mockery_parentClass . '::' . $name, $args);
}




public function mockery_getMockableMethods()
{
return $this->_mockery_mockableMethods;
}




public function mockery_isAnonymous()
{
$rfc = new \ReflectionClass($this);


$interfaces = array_filter($rfc->getInterfaces(), static function ($i) {
return $i->getName() !== 'Stringable';
});

return false === $rfc->getParentClass() && 2 === count($interfaces);
}

public function mockery_isInstance()
{
return $this->_mockery_instanceMock;
}

public function __wakeup()
{






}

public function __destruct()
{



}

public function mockery_getMethod($name)
{
foreach ($this->mockery_getMethods() as $method) {
if ($method->getName() == $name) {
return $method;
}
}

return null;
}






public function mockery_returnValueForMethod($name)
{
$rm = $this->mockery_getMethod($name);

if ($rm === null) {
return null;
}

$returnType = Reflector::getSimplestReturnType($rm);

switch ($returnType) {
case null: return null;
case 'string': return '';
case 'int': return 0;
case 'float': return 0.0;
case 'bool': return false;
case 'true': return true;
case 'false': return false;

case 'array':
case 'iterable':
return [];

case 'callable':
case '\Closure':
return static function () : void {
};

case '\Traversable':
case '\Generator':
$generator = static function () {
yield;
};
return $generator();

case 'void':
return null;

case 'static':
return $this;

case 'object':
$mock = \Mockery::mock();
if ($this->_mockery_ignoreMissingRecursive) {
$mock->shouldIgnoreMissing($this->_mockery_defaultReturnValue, true);
}

return $mock;

default:
$mock = \Mockery::mock($returnType);
if ($this->_mockery_ignoreMissingRecursive) {
$mock->shouldIgnoreMissing($this->_mockery_defaultReturnValue, true);
}

return $mock;
}
}

public function shouldHaveReceived($method = null, $args = null)
{
if ($method === null) {
return new HigherOrderMessage($this, 'shouldHaveReceived');
}

$expectation = new VerificationExpectation($this, $method);
if (null !== $args) {
$expectation->withArgs($args);
}

$expectation->atLeast()->once();
$director = new VerificationDirector($this->_mockery_getReceivedMethodCalls(), $expectation);
++$this->_mockery_expectations_count;
$director->verify();
return $director;
}

public function shouldHaveBeenCalled()
{
return $this->shouldHaveReceived('__invoke');
}

public function shouldNotHaveReceived($method = null, $args = null)
{
if ($method === null) {
return new HigherOrderMessage($this, 'shouldNotHaveReceived');
}

$expectation = new VerificationExpectation($this, $method);
if (null !== $args) {
$expectation->withArgs($args);
}

$expectation->never();
$director = new VerificationDirector($this->_mockery_getReceivedMethodCalls(), $expectation);
++$this->_mockery_expectations_count;
$director->verify();
return null;
}

public function shouldNotHaveBeenCalled(?array $args = null)
{
return $this->shouldNotHaveReceived('__invoke', $args);
}

protected static function _mockery_handleStaticMethodCall($method, array $args)
{
$associatedRealObject = \Mockery::fetchMock(self::class);
try {
return $associatedRealObject->__call($method, $args);
} catch (BadMethodCallException $badMethodCallException) {
throw new BadMethodCallException(
'Static method ' . $associatedRealObject->mockery_getName() . '::' . $method
. '() does not exist on this mock object',
0,
$badMethodCallException
);
}
}

protected function _mockery_getReceivedMethodCalls()
{
return $this->_mockery_receivedMethodCalls ?: $this->_mockery_receivedMethodCalls = new ReceivedMethodCalls();
}







protected function _mockery_constructorCalled(array $args)
{
if (!isset($this->_mockery_expectations['__construct']) ) {
return;
}

$this->_mockery_handleMethodCall('__construct', $args);
}

protected function _mockery_findExpectedMethodHandler($method)
{
if (isset($this->_mockery_expectations[$method])) {
return $this->_mockery_expectations[$method];
}

$lowerCasedMockeryExpectations = array_change_key_case($this->_mockery_expectations, CASE_LOWER);
$lowerCasedMethod = strtolower($method);

return $lowerCasedMockeryExpectations[$lowerCasedMethod] ?? null;
}

protected function _mockery_handleMethodCall($method, array $args)
{
$this->_mockery_getReceivedMethodCalls()->push(new MethodCall($method, $args));

$rm = $this->mockery_getMethod($method);
if ($rm && $rm->isProtected() && !$this->_mockery_allowMockingProtectedMethods) {
if ($rm->isAbstract()) {
return;
}

try {
$prototype = $rm->getPrototype();
if ($prototype->isAbstract()) {
return;
}
} catch (\ReflectionException $re) {

}

if (null === $this->_mockery_parentClass) {
$this->_mockery_parentClass = get_parent_class($this);
}

return call_user_func_array($this->_mockery_parentClass . '::' . $method, $args);
}

$handler = $this->_mockery_findExpectedMethodHandler($method);

if ($handler !== null && !$this->_mockery_disableExpectationMatching) {
try {
return $handler->call($args);
} catch (NoMatchingExpectationException $e) {
if (!$this->_mockery_ignoreMissing && !$this->_mockery_deferMissing) {
throw $e;
}
}
}

if (!is_null($this->_mockery_partial) &&
(method_exists($this->_mockery_partial, $method) || method_exists($this->_mockery_partial, '__call'))) {
return $this->_mockery_partial->{$method}(...$args);
}

if ($this->_mockery_deferMissing && is_callable($this->_mockery_parentClass . '::' . $method)
&& (!$this->hasMethodOverloadingInParentClass() || ($this->_mockery_parentClass && method_exists($this->_mockery_parentClass, $method)))) {
return call_user_func_array($this->_mockery_parentClass . '::' . $method, $args);
}

if ($this->_mockery_deferMissing && $this->_mockery_parentClass && method_exists($this->_mockery_parentClass, '__call')) {
return call_user_func($this->_mockery_parentClass . '::__call', $method, $args);
}

if ($method === '__toString') {



return sprintf('%s#%s', self::class, spl_object_hash($this));
}

if ($this->_mockery_ignoreMissing && (\Mockery::getConfiguration()->mockingNonExistentMethodsAllowed() || (!is_null($this->_mockery_partial) && method_exists($this->_mockery_partial, $method)) || is_callable($this->_mockery_parentClass . '::' . $method))) {
if ($this->_mockery_defaultReturnValue instanceof Undefined) {
return $this->_mockery_defaultReturnValue->{$method}(...$args);
}

if (null === $this->_mockery_defaultReturnValue) {
return $this->mockery_returnValueForMethod($method);
}

return $this->_mockery_defaultReturnValue;
}

$message = 'Method ' . self::class . '::' . $method .
'() does not exist on this mock object';

if (!is_null($rm)) {
$message = 'Received ' . self::class .
'::' . $method . '(), but no expectations were specified';
}

$bmce = new BadMethodCallException($message);
$this->_mockery_thrownExceptions[] = $bmce;
throw $bmce;
}







protected function mockery_getMethods()
{
if (static::$_mockery_methods && \Mockery::getConfiguration()->reflectionCacheEnabled()) {
return static::$_mockery_methods;
}

if ($this->_mockery_partial !== null) {
$reflected = new \ReflectionObject($this->_mockery_partial);
} else {
$reflected = new \ReflectionClass($this);
}

return static::$_mockery_methods = $reflected->getMethods();
}

private function hasMethodOverloadingInParentClass()
{

return is_callable($this->_mockery_parentClass . '::aFunctionNameThatNoOneWouldEverUseInRealLife12345');
}




private function getNonPublicMethods()
{
return array_map(
static function ($method) {
return $method->getName();
},
array_filter($this->mockery_getMethods(), static function ($method) {
return !$method->isPublic();
})
);
}
}
