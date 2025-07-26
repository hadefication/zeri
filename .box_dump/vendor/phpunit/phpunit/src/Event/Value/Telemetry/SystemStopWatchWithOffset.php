<?php declare(strict_types=1);








namespace PHPUnit\Event\Telemetry;

use function hrtime;
use PHPUnit\Event\InvalidArgumentException;

/**
@no-named-arguments




*/
final class SystemStopWatchWithOffset implements StopWatch
{
private ?HRTime $offset;

public function __construct(HRTime $offset)
{
$this->offset = $offset;
}




public function current(): HRTime
{
if ($this->offset !== null) {
$offset = $this->offset;

$this->offset = null;

return $offset;
}

return HRTime::fromSecondsAndNanoseconds(...hrtime());
}
}
