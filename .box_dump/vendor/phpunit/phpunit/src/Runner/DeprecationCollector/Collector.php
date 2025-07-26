<?php declare(strict_types=1);








namespace PHPUnit\Runner\DeprecationCollector;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\UnknownSubscriberTypeException;
use PHPUnit\TestRunner\IssueFilter;

/**
@no-named-arguments


*/
final class Collector
{
private readonly IssueFilter $issueFilter;




private array $deprecations = [];




private array $filteredDeprecations = [];





public function __construct(Facade $facade, IssueFilter $issueFilter)
{
$facade->registerSubscribers(
new TestPreparedSubscriber($this),
new TestTriggeredDeprecationSubscriber($this),
);

$this->issueFilter = $issueFilter;
}




public function deprecations(): array
{
return $this->deprecations;
}




public function filteredDeprecations(): array
{
return $this->filteredDeprecations;
}

public function testPrepared(): void
{
$this->deprecations = [];
}

public function testTriggeredDeprecation(DeprecationTriggered $event): void
{
$this->deprecations[] = $event->message();

if (!$this->issueFilter->shouldBeProcessed($event)) {
return;
}

$this->filteredDeprecations[] = $event->message();
}
}
