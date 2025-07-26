<?php declare(strict_types=1);








namespace PHPUnit\Runner\GarbageCollection;

use function gc_collect_cycles;
use function gc_disable;
use function gc_enable;
use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\UnknownSubscriberTypeException;

/**
@no-named-arguments


*/
final class GarbageCollectionHandler
{
private readonly Facade $facade;
private readonly int $threshold;
private int $tests = 0;





public function __construct(Facade $facade, int $threshold)
{
$this->facade = $facade;
$this->threshold = $threshold;

$this->registerSubscribers();
}

public function executionStarted(): void
{
gc_disable();

$this->facade->emitter()->testRunnerDisabledGarbageCollection();

gc_collect_cycles();

$this->facade->emitter()->testRunnerTriggeredGarbageCollection();
}

public function executionFinished(): void
{
gc_collect_cycles();

$this->facade->emitter()->testRunnerTriggeredGarbageCollection();

gc_enable();

$this->facade->emitter()->testRunnerEnabledGarbageCollection();
}

public function testFinished(): void
{
$this->tests++;

if ($this->tests === $this->threshold) {
gc_collect_cycles();

$this->facade->emitter()->testRunnerTriggeredGarbageCollection();

$this->tests = 0;
}
}





private function registerSubscribers(): void
{
$this->facade->registerSubscribers(
new ExecutionStartedSubscriber($this),
new ExecutionFinishedSubscriber($this),
new TestFinishedSubscriber($this),
);
}
}
