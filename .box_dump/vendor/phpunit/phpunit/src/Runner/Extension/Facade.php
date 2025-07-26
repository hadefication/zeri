<?php declare(strict_types=1);








namespace PHPUnit\Runner\Extension;

use PHPUnit\Event\EventFacadeIsSealedException;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Event\Subscriber;
use PHPUnit\Event\Tracer\Tracer;
use PHPUnit\Event\UnknownSubscriberTypeException;

/**
@no-named-arguments
*/
final class Facade
{
private bool $replacesOutput = false;
private bool $replacesProgressOutput = false;
private bool $replacesResultOutput = false;
private bool $requiresCodeCoverageCollection = false;





public function registerSubscribers(Subscriber ...$subscribers): void
{
EventFacade::instance()->registerSubscribers(...$subscribers);
}





public function registerSubscriber(Subscriber $subscriber): void
{
EventFacade::instance()->registerSubscriber($subscriber);
}




public function registerTracer(Tracer $tracer): void
{
EventFacade::instance()->registerTracer($tracer);
}

public function replaceOutput(): void
{
$this->replacesOutput = true;
}

public function replacesOutput(): bool
{
return $this->replacesOutput;
}

public function replaceProgressOutput(): void
{
$this->replacesProgressOutput = true;
}

public function replacesProgressOutput(): bool
{
return $this->replacesOutput || $this->replacesProgressOutput;
}

public function replaceResultOutput(): void
{
$this->replacesResultOutput = true;
}

public function replacesResultOutput(): bool
{
return $this->replacesOutput || $this->replacesResultOutput;
}

public function requireCodeCoverageCollection(): void
{
$this->requiresCodeCoverageCollection = true;
}

public function requiresCodeCoverageCollection(): bool
{
return $this->requiresCodeCoverageCollection;
}
}
