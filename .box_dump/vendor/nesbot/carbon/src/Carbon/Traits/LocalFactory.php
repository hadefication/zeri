<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Factory;
use Carbon\FactoryImmutable;
use Carbon\WrapperClock;
use Closure;




trait LocalFactory
{



private ?WrapperClock $clock = null;

public function getClock(): ?WrapperClock
{
return $this->clock;
}

private function initLocalFactory(): void
{
$this->clock = FactoryImmutable::getCurrentClock();
}

/**
@template







*/
private function transmitFactory(Closure $action): mixed
{
$previousClock = FactoryImmutable::getCurrentClock();
FactoryImmutable::setCurrentClock($this->clock);

try {
return $action();
} finally {
FactoryImmutable::setCurrentClock($previousClock);
}
}

private function getFactory(): Factory
{
return $this->getClock()?->getFactory() ?? FactoryImmutable::getDefaultInstance();
}
}
