<?php declare(strict_types=1);








namespace SebastianBergmann\Timer;

use function array_pop;
use function hrtime;

final class Timer
{



private array $startTimes = [];

public function start(): void
{
$this->startTimes[] = (float) hrtime(true);
}




public function stop(): Duration
{
if (empty($this->startTimes)) {
throw new NoActiveTimerException(
'Timer::start() has to be called before Timer::stop()',
);
}

return Duration::fromNanoseconds((float) hrtime(true) - array_pop($this->startTimes));
}
}
