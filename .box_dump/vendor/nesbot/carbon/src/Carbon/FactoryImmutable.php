<?php

declare(strict_types=1);










namespace Carbon;

use Closure;
use DateTimeInterface;
use DateTimeZone;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\NativeClock;
use Symfony\Contracts\Translation\TranslatorInterface;












































































































class FactoryImmutable extends Factory implements ClockInterface
{
protected string $className = CarbonImmutable::class;

private static ?self $defaultInstance = null;

private static ?WrapperClock $currentClock = null;




public static function getDefaultInstance(): self
{
return self::$defaultInstance ??= new self();
}




public static function getInstance(): Factory
{
return self::$currentClock?->getFactory() ?? self::getDefaultInstance();
}




public static function setCurrentClock(ClockInterface|Factory|DateTimeInterface|null $currentClock): void
{
if ($currentClock && !($currentClock instanceof WrapperClock)) {
$currentClock = new WrapperClock($currentClock);
}

self::$currentClock = $currentClock;
}




public static function getCurrentClock(): ?WrapperClock
{
return self::$currentClock;
}




public function now(DateTimeZone|string|int|null $timezone = null): CarbonImmutable
{
return $this->__call('now', [$timezone]);
}

public function sleep(int|float $seconds): void
{
if ($this->hasTestNow()) {
$this->setTestNow($this->getTestNow()->avoidMutation()->addSeconds($seconds));

return;
}

(new NativeClock('UTC'))->sleep($seconds);
}
}
