<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Traits\Date;
use DateTimeImmutable;
use DateTimeInterface;

/**
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read
@property-read






































































































































































































































































































































































































































































































































































































































































































































*/
class CarbonImmutable extends DateTimeImmutable implements CarbonInterface
{
use Date {
__clone as dateTraitClone;
}

public function __clone(): void
{
$this->dateTraitClone();
$this->endOfTime = false;
$this->startOfTime = false;
}






public static function startOfTime(): static
{
$date = static::parse('0001-01-01')->years(self::getStartOfTimeYear());
$date->startOfTime = true;

return $date;
}






public static function endOfTime(): static
{
$date = static::parse('9999-12-31 23:59:59.999999')->years(self::getEndOfTimeYear());
$date->endOfTime = true;

return $date;
}




private static function getEndOfTimeYear(): int
{
return 1118290769066902787; 
}




private static function getStartOfTimeYear(): int
{
return -1118290769066898816; 
}
}
