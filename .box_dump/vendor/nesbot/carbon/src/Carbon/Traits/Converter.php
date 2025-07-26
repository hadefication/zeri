<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\CarbonPeriodImmutable;
use Carbon\Exceptions\UnitException;
use Closure;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;











trait Converter
{
use ToStringFormat;






public function format(string $format): string
{
$function = $this->localFormatFunction
?? $this->getFactory()->getSettings()['formatFunction']
?? static::$formatFunction;

if (!$function) {
return $this->rawFormat($format);
}

if (\is_string($function) && method_exists($this, $function)) {
$function = [$this, $function];
}

return $function(...\func_get_args());
}




public function rawFormat(string $format): string
{
return parent::format($format);
}









public function __toString(): string
{
$format = $this->localToStringFormat
?? $this->getFactory()->getSettings()['toStringFormat']
?? null;

return $format instanceof Closure
? $format($this)
: $this->rawFormat($format ?: (
\defined('static::DEFAULT_TO_STRING_FORMAT')
? static::DEFAULT_TO_STRING_FORMAT
: CarbonInterface::DEFAULT_TO_STRING_FORMAT
));
}









public function toDateString(): string
{
return $this->rawFormat('Y-m-d');
}









public function toFormattedDateString(): string
{
return $this->rawFormat('M j, Y');
}









public function toFormattedDayDateString(): string
{
return $this->rawFormat('D, M j, Y');
}









public function toTimeString(string $unitPrecision = 'second'): string
{
return $this->rawFormat(static::getTimeFormatByPrecision($unitPrecision));
}









public function toDateTimeString(string $unitPrecision = 'second'): string
{
return $this->rawFormat('Y-m-d '.static::getTimeFormatByPrecision($unitPrecision));
}






public static function getTimeFormatByPrecision(string $unitPrecision): string
{
return match (static::singularUnit($unitPrecision)) {
'minute' => 'H:i',
'second' => 'H:i:s',
'm', 'millisecond' => 'H:i:s.v',
'µ', 'microsecond' => 'H:i:s.u',
default => throw new UnitException('Precision unit expected among: minute, second, millisecond and microsecond.'),
};
}











public function toDateTimeLocalString(string $unitPrecision = 'second'): string
{
return $this->rawFormat('Y-m-d\T'.static::getTimeFormatByPrecision($unitPrecision));
}









public function toDayDateTimeString(): string
{
return $this->rawFormat('D, M j, Y g:i A');
}









public function toAtomString(): string
{
return $this->rawFormat(DateTime::ATOM);
}









public function toCookieString(): string
{
return $this->rawFormat(DateTimeInterface::COOKIE);
}









public function toIso8601String(): string
{
return $this->toAtomString();
}









public function toRfc822String(): string
{
return $this->rawFormat(DateTimeInterface::RFC822);
}









public function toIso8601ZuluString(string $unitPrecision = 'second'): string
{
return $this->avoidMutation()
->utc()
->rawFormat('Y-m-d\T'.static::getTimeFormatByPrecision($unitPrecision).'\Z');
}









public function toRfc850String(): string
{
return $this->rawFormat(DateTimeInterface::RFC850);
}









public function toRfc1036String(): string
{
return $this->rawFormat(DateTimeInterface::RFC1036);
}









public function toRfc1123String(): string
{
return $this->rawFormat(DateTimeInterface::RFC1123);
}









public function toRfc2822String(): string
{
return $this->rawFormat(DateTimeInterface::RFC2822);
}










public function toRfc3339String(bool $extended = false): string
{
return $this->rawFormat($extended ? DateTimeInterface::RFC3339_EXTENDED : DateTimeInterface::RFC3339);
}









public function toRssString(): string
{
return $this->rawFormat(DateTimeInterface::RSS);
}









public function toW3cString(): string
{
return $this->rawFormat(DateTimeInterface::W3C);
}









public function toRfc7231String(): string
{
return $this->avoidMutation()
->setTimezone('GMT')
->rawFormat(\defined('static::RFC7231_FORMAT') ? static::RFC7231_FORMAT : CarbonInterface::RFC7231_FORMAT);
}









public function toArray(): array
{
return [
'year' => $this->year,
'month' => $this->month,
'day' => $this->day,
'dayOfWeek' => $this->dayOfWeek,
'dayOfYear' => $this->dayOfYear,
'hour' => $this->hour,
'minute' => $this->minute,
'second' => $this->second,
'micro' => $this->micro,
'timestamp' => $this->timestamp,
'formatted' => $this->rawFormat(\defined('static::DEFAULT_TO_STRING_FORMAT') ? static::DEFAULT_TO_STRING_FORMAT : CarbonInterface::DEFAULT_TO_STRING_FORMAT),
'timezone' => $this->timezone,
];
}









public function toObject(): object
{
return (object) $this->toArray();
}









public function toString(): string
{
return $this->avoidMutation()->locale('en')->isoFormat('ddd MMM DD YYYY HH:mm:ss [GMT]ZZ');
}













public function toISOString(bool $keepOffset = false): ?string
{
if (!$this->isValid()) {
return null;
}

$yearFormat = $this->year < 0 || $this->year > 9999 ? 'YYYYYY' : 'YYYY';
$timezoneFormat = $keepOffset ? 'Z' : '[Z]';
$date = $keepOffset ? $this : $this->avoidMutation()->utc();

return $date->isoFormat("$yearFormat-MM-DD[T]HH:mm:ss.SSSSSS$timezoneFormat");
}









public function toJSON(): ?string
{
return $this->toISOString();
}









public function toDateTime(): DateTime
{
return DateTime::createFromFormat('U.u', $this->rawFormat('U.u'))
->setTimezone($this->getTimezone());
}









public function toDateTimeImmutable(): DateTimeImmutable
{
return DateTimeImmutable::createFromFormat('U.u', $this->rawFormat('U.u'))
->setTimezone($this->getTimezone());
}

/**
@alias







*/
public function toDate(): DateTime
{
return $this->toDateTime();
}








public function toPeriod($end = null, $interval = null, $unit = null): CarbonPeriod
{
if ($unit) {
$interval = CarbonInterval::make("$interval ".static::pluralUnit($unit));
}

$isDefaultInterval = !$interval;
$interval ??= CarbonInterval::day();
$class = $this->isMutable() ? CarbonPeriod::class : CarbonPeriodImmutable::class;

if (\is_int($end) || (\is_string($end) && ctype_digit($end))) {
$end = (int) $end;
}

$end ??= 1;

if (!\is_int($end)) {
$end = $this->resolveCarbon($end);
}

return new $class(
raw: [$this, CarbonInterval::make($interval), $end],
dateClass: static::class,
isDefaultInterval: $isDefaultInterval,
);
}








public function range($end = null, $interval = null, $unit = null): CarbonPeriod
{
return $this->toPeriod($end, $interval, $unit);
}
}
