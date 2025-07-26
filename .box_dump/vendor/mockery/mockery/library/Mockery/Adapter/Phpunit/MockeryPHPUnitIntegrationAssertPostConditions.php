<?php









declare(strict_types=1);

namespace Mockery\Adapter\Phpunit;

trait MockeryPHPUnitIntegrationAssertPostConditions
{
protected function assertPostConditions(): void
{
$this->mockeryAssertPostConditions();
}
}
