<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use function array_merge;
use function assert;
use function debug_backtrace;
use function trait_exists;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\InvalidArgumentException;
use PHPUnit\Framework\MockObject\Generator\CannotUseAddMethodsException;
use PHPUnit\Framework\MockObject\Generator\ClassIsEnumerationException;
use PHPUnit\Framework\MockObject\Generator\ClassIsFinalException;
use PHPUnit\Framework\MockObject\Generator\DuplicateMethodException;
use PHPUnit\Framework\MockObject\Generator\Generator;
use PHPUnit\Framework\MockObject\Generator\InvalidMethodNameException;
use PHPUnit\Framework\MockObject\Generator\NameAlreadyInUseException;
use PHPUnit\Framework\MockObject\Generator\OriginalConstructorInvocationRequiredException;
use PHPUnit\Framework\MockObject\Generator\ReflectionException;
use PHPUnit\Framework\MockObject\Generator\RuntimeException;
use PHPUnit\Framework\MockObject\Generator\UnknownTypeException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
@template
@no-named-arguments

*/
final class MockBuilder
{
private readonly TestCase $testCase;




private readonly string $type;




private array $methods = [];
private bool $emptyMethodsArray = false;




private ?string $mockClassName = null;




private array $constructorArgs = [];
private bool $originalConstructor = true;
private bool $originalClone = true;
private bool $autoload = true;
private bool $cloneArguments = false;
private bool $callOriginalMethods = false;
private ?object $proxyTarget = null;
private bool $allowMockingUnknownTypes = true;
private bool $returnValueGeneration = true;
private readonly Generator $generator;




public function __construct(TestCase $testCase, string $type)
{
$this->testCase = $testCase;
$this->type = $type;
$this->generator = new Generator;
}

















public function getMock(): MockObject
{
$object = $this->generator->testDouble(
$this->type,
true,
true,
!$this->emptyMethodsArray ? $this->methods : null,
$this->constructorArgs,
$this->mockClassName ?? '',
$this->originalConstructor,
$this->originalClone,
$this->autoload,
$this->cloneArguments,
$this->callOriginalMethods,
$this->proxyTarget,
$this->allowMockingUnknownTypes,
$this->returnValueGeneration,
);

assert($object instanceof $this->type);
assert($object instanceof MockObject);

$this->testCase->registerMockObject($object);

return $object;
}












public function getMockForAbstractClass(): MockObject
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::getMockForAbstractClass() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$object = $this->generator->mockObjectForAbstractClass(
$this->type,
$this->constructorArgs,
$this->mockClassName ?? '',
$this->originalConstructor,
$this->originalClone,
$this->autoload,
$this->methods,
$this->cloneArguments,
);

assert($object instanceof MockObject);

$this->testCase->registerMockObject($object);

return $object;
}












public function getMockForTrait(): MockObject
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::getMockForTrait() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

assert(trait_exists($this->type));

$object = $this->generator->mockObjectForTrait(
$this->type,
$this->constructorArgs,
$this->mockClassName ?? '',
$this->originalConstructor,
$this->originalClone,
$this->autoload,
$this->methods,
$this->cloneArguments,
);

assert($object instanceof MockObject);

$this->testCase->registerMockObject($object);

return $object;
}











public function onlyMethods(array $methods): self
{
if (empty($methods)) {
$this->emptyMethodsArray = true;

return $this;
}

try {
$reflector = new ReflectionClass($this->type);


/**
@phpstan-ignore */
} catch (\ReflectionException $e) {
throw new ReflectionException(
$e->getMessage(),
$e->getCode(),
$e,
);

}

foreach ($methods as $method) {
if (!$reflector->hasMethod($method)) {
throw new CannotUseOnlyMethodsException($this->type, $method);
}
}

$this->methods = array_merge($this->methods, $methods);

return $this;
}














public function addMethods(array $methods): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::addMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

if (empty($methods)) {
$this->emptyMethodsArray = true;

return $this;
}

try {
$reflector = new ReflectionClass($this->type);


/**
@phpstan-ignore */
} catch (\ReflectionException $e) {
throw new ReflectionException(
$e->getMessage(),
$e->getCode(),
$e,
);

}

foreach ($methods as $method) {
if ($reflector->hasMethod($method)) {
throw new CannotUseAddMethodsException($this->type, $method);
}
}

$this->methods = array_merge($this->methods, $methods);

return $this;
}








public function setConstructorArgs(array $arguments): self
{
$this->constructorArgs = $arguments;

return $this;
}








public function setMockClassName(string $name): self
{
$this->mockClassName = $name;

return $this;
}






public function disableOriginalConstructor(): self
{
$this->originalConstructor = false;

return $this;
}






public function enableOriginalConstructor(): self
{
$this->originalConstructor = true;

return $this;
}






public function disableOriginalClone(): self
{
$this->originalClone = false;

return $this;
}






public function enableOriginalClone(): self
{
$this->originalClone = true;

return $this;
}










public function disableAutoload(): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::disableAutoload() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->autoload = false;

return $this;
}








public function enableAutoload(): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::enableAutoload() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->autoload = true;

return $this;
}








public function disableArgumentCloning(): self
{
if (!$this->calledFromTestCase()) {
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::disableArgumentCloning() is deprecated and will be removed in PHPUnit 12 without replacement.',
);
}

$this->cloneArguments = false;

return $this;
}








public function enableArgumentCloning(): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::enableArgumentCloning() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->cloneArguments = true;

return $this;
}










public function enableProxyingToOriginalMethods(): self
{
if (!$this->calledFromTestCase()) {
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::enableProxyingToOriginalMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
);
}

$this->callOriginalMethods = true;

return $this;
}








public function disableProxyingToOriginalMethods(): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::disableProxyingToOriginalMethods() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->callOriginalMethods = false;
$this->proxyTarget = null;

return $this;
}










public function setProxyTarget(object $object): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::setProxyTarget() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->proxyTarget = $object;

return $this;
}






public function allowMockingUnknownTypes(): self
{
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::allowMockingUnknownTypes() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$this->allowMockingUnknownTypes = true;

return $this;
}






public function disallowMockingUnknownTypes(): self
{
if (!$this->calledFromTestCase()) {
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$this->testCase->valueObjectForEvents(),
'MockBuilder::disallowMockingUnknownTypes() is deprecated and will be removed in PHPUnit 12 without replacement.',
);
}

$this->allowMockingUnknownTypes = false;

return $this;
}




public function enableAutoReturnValueGeneration(): self
{
$this->returnValueGeneration = true;

return $this;
}




public function disableAutoReturnValueGeneration(): self
{
$this->returnValueGeneration = false;

return $this;
}

private function calledFromTestCase(): bool
{
$caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, limit: 3)[2];

return isset($caller['class']) && $caller['class'] === TestCase::class;
}
}
