<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function assert;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Event\Code\TestMethodBuilder;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker as InvocationMockerBuilder;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;

/**
@no-named-arguments


*/
trait MockObjectApi
{
/**
@noinspection */
public function __phpunit_hasMatchers(): bool
{
return $this->__phpunit_getInvocationHandler()->hasMatchers();
}

/**
@noinspection */
public function __phpunit_verify(bool $unsetInvocationMocker = true): void
{
$this->__phpunit_getInvocationHandler()->verify();

if ($unsetInvocationMocker) {
$this->__phpunit_unsetInvocationMocker();
}
}

abstract public function __phpunit_state(): TestDoubleState;

abstract public function __phpunit_getInvocationHandler(): InvocationHandler;

abstract public function __phpunit_unsetInvocationMocker(): void;

public function expects(InvocationOrder $matcher): InvocationMockerBuilder
{
assert($this instanceof StubInternal);

if (!$this->__phpunit_wasGeneratedAsMockObject()) {
$message = 'Expectations configured on test doubles that are created as test stubs are no longer verified since PHPUnit 10. Test doubles that are created as test stubs will no longer have the expects() method in PHPUnit 12. Update your test code to use createMock() instead of createStub(), for example.';

try {
$test = TestMethodBuilder::fromCallStack();

if (!$this->__phpunit_state()->wasDeprecationAlreadyEmittedFor($test->id())) {
EventFacade::emitter()->testTriggeredPhpunitDeprecation(
$test,
$message,
);

$this->__phpunit_state()->deprecationWasEmittedFor($test->id());
}

} catch (NoTestCaseObjectOnCallStackException) {
EventFacade::emitter()->testRunnerTriggeredPhpunitDeprecation($message);

}
}

return $this->__phpunit_getInvocationHandler()->expects($matcher);
}
}
