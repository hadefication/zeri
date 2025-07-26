<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\CarbonInterface;
use Carbon\CarbonTimeZone;
use Carbon\Factory;
use Carbon\FactoryImmutable;
use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

trait Test
{


























public static function setTestNow(mixed $testNow = null): void
{
FactoryImmutable::getDefaultInstance()->setTestNow($testNow);
}




















public static function setTestNowAndTimezone($testNow = null, $timezone = null): void
{
FactoryImmutable::getDefaultInstance()->setTestNowAndTimezone($testNow, $timezone);
}

/**
@template











*/
public static function withTestNow(mixed $testNow, callable $callback): mixed
{
return FactoryImmutable::getDefaultInstance()->withTestNow($testNow, $callback);
}







public static function getTestNow(): Closure|CarbonInterface|null
{
return FactoryImmutable::getInstance()->getTestNow();
}







public static function hasTestNow(): bool
{
return FactoryImmutable::getInstance()->hasTestNow();
}




protected static function getMockedTestNow(DateTimeZone|string|int|null $timezone): ?CarbonInterface
{
$testNow = FactoryImmutable::getInstance()->handleTestNowClosure(static::getTestNow(), $timezone);

if ($testNow === null) {
return null;
}

$testNow = $testNow->avoidMutation();

return $timezone ? $testNow->setTimezone($timezone) : $testNow;
}

private function mockConstructorParameters(&$time, ?CarbonTimeZone $timezone): void
{
$clock = $this->clock?->unwrap();
$now = $clock instanceof Factory
? $clock->getTestNow()
: $this->nowFromClock($timezone);
$testInstance = $now ?? self::getMockedTestNowClone($timezone);

if (!$testInstance) {
return;
}

if ($testInstance instanceof DateTimeInterface) {
$testInstance = $testInstance->setTimezone($timezone ?? date_default_timezone_get());
}

if (static::hasRelativeKeywords($time)) {
$testInstance = $testInstance->modify($time);
}

$factory = $this->getClock()?->unwrap();

if (!($factory instanceof Factory)) {
$factory = FactoryImmutable::getInstance();
}

$testInstance = $factory->handleTestNowClosure($testInstance, $timezone);

$time = $testInstance instanceof self
? $testInstance->rawFormat(static::MOCK_DATETIME_FORMAT)
: $testInstance->format(static::MOCK_DATETIME_FORMAT);
}

private function getMockedTestNowClone($timezone): CarbonInterface|self|null
{
$mock = static::getMockedTestNow($timezone);

return $mock ? clone $mock : null;
}

private function nowFromClock(?CarbonTimeZone $timezone): ?DateTimeImmutable
{
$now = $this->clock?->now();

return $now && $timezone ? $now->setTimezone($timezone) : null;
}
}
