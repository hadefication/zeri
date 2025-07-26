<?php









namespace Mockery\Adapter\Phpunit;

use PHPUnit\Framework\TestCase;

abstract class MockeryTestCase extends TestCase
{
use MockeryPHPUnitIntegration;
use MockeryTestCaseSetUp;

protected function mockeryTestSetUp()
{
}

protected function mockeryTestTearDown()
{
}
}
