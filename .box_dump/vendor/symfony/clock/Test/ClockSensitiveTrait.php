<?php










namespace Symfony\Component\Clock\Test;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\BeforeClass;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

use function Symfony\Component\Clock\now;











trait ClockSensitiveTrait
{
public static function mockTime(string|\DateTimeImmutable|bool $when = true): ClockInterface
{
Clock::set(match (true) {
false === $when => self::saveClockBeforeTest(false),
true === $when => new MockClock(),
$when instanceof \DateTimeImmutable => new MockClock($when),
default => new MockClock(now($when)),
});

return Clock::get();
}

/**
@beforeClass
@before



*/
#[Before]
#[BeforeClass]
public static function saveClockBeforeTest(bool $save = true): ClockInterface
{
static $originalClock;

if ($save && $originalClock) {
self::restoreClockAfterTest();
}

return $save ? $originalClock = Clock::get() : $originalClock;
}

/**
@after


*/
#[After]
protected static function restoreClockAfterTest(): void
{
Clock::set(self::saveClockBeforeTest(false));
}
}
