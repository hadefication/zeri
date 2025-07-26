<?php









namespace Mockery\Adapter\Phpunit;

use Mockery;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

use function method_exists;





trait MockeryPHPUnitIntegration
{
use MockeryPHPUnitIntegrationAssertPostConditions;

protected $mockeryOpen;

protected function addMockeryExpectationsToAssertionCount()
{
$this->addToAssertionCount(Mockery::getContainer()->mockery_getExpectationCount());
}

protected function checkMockeryExceptions()
{
if (! method_exists($this, 'markAsRisky')) {
return;
}

foreach (Mockery::getContainer()->mockery_thrownExceptions() as $e) {
if (! $e->dismissed()) {
$this->markAsRisky();
}
}
}

protected function closeMockery()
{
Mockery::close();
$this->mockeryOpen = false;
}





protected function mockeryAssertPostConditions()
{
$this->addMockeryExpectationsToAssertionCount();
$this->checkMockeryExceptions();
$this->closeMockery();

parent::assertPostConditions();
}

/**
@after
*/
#[After]
protected function purgeMockeryContainer()
{
if ($this->mockeryOpen) {

Mockery::close();
}
}

/**
@before
*/
#[Before]
protected function startMockery()
{
$this->mockeryOpen = true;
}
}
