<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Carbon\Exceptions\UnknownUnitException;
use Carbon\Unit;
use Closure;
use DateInterval;
use DateTimeInterface;










trait Difference
{










public function diffAsDateInterval($date = null, bool $absolute = false): DateInterval
{
$other = $this->resolveCarbon($date);





if ($other->tz !== $this->tz) {
$other = $other->avoidMutation()->setTimezone($this->tz);
}

return parent::diff($other, $absolute);
}











public function diffAsCarbonInterval($date = null, bool $absolute = false, array $skip = []): CarbonInterval
{
return CarbonInterval::diff($this, $this->resolveCarbon($date), $absolute, $skip)
->setLocalTranslator($this->getLocalTranslator());
}

/**
@alias









*/
public function diff($date = null, bool $absolute = false, array $skip = []): CarbonInterval
{
return $this->diffAsCarbonInterval($date, $absolute, $skip);
}











public function diffInUnit(Unit|string $unit, $date = null, bool $absolute = false, bool $utc = false): float
{
$unit = static::pluralUnit($unit instanceof Unit ? $unit->value : rtrim($unit, 'z'));
$method = 'diffIn'.$unit;

if (!method_exists($this, $method)) {
throw new UnknownUnitException($unit);
}

return $this->$method($date, $absolute, $utc);
}










public function diffInYears($date = null, bool $absolute = false, bool $utc = false): float
{
$start = $this;
$end = $this->resolveCarbon($date);

if ($utc) {
$start = $start->avoidMutation()->utc();
$end = $end->avoidMutation()->utc();
}

$ascending = ($start <= $end);
$sign = $absolute || $ascending ? 1 : -1;

if (!$ascending) {
[$start, $end] = [$end, $start];
}

$yearsDiff = (int) $start->diff($end, $absolute)->format('%r%y');

$floorEnd = $start->avoidMutation()->addYears($yearsDiff);

if ($floorEnd >= $end) {
return $sign * $yearsDiff;
}


$ceilEnd = $start->avoidMutation()->addYears($yearsDiff + 1);

$daysToFloor = $floorEnd->diffInDays($end);
$daysToCeil = $end->diffInDays($ceilEnd);

return $sign * ($yearsDiff + $daysToFloor / ($daysToCeil + $daysToFloor));
}










public function diffInQuarters($date = null, bool $absolute = false, bool $utc = false): float
{
return $this->diffInMonths($date, $absolute, $utc) / static::MONTHS_PER_QUARTER;
}










public function diffInMonths($date = null, bool $absolute = false, bool $utc = false): float
{
$start = $this;
$end = $this->resolveCarbon($date);


if ($utc || ($end->timezoneName !== $start->timezoneName)) {
$start = $start->avoidMutation()->utc();
$end = $end->avoidMutation()->utc();
}

[$yearStart, $monthStart, $dayStart] = explode('-', $start->format('Y-m-dHisu'));
[$yearEnd, $monthEnd, $dayEnd] = explode('-', $end->format('Y-m-dHisu'));

$monthsDiff = (((int) $yearEnd) - ((int) $yearStart)) * static::MONTHS_PER_YEAR +
((int) $monthEnd) - ((int) $monthStart);

if ($monthsDiff > 0) {
$monthsDiff -= ($dayStart > $dayEnd ? 1 : 0);
} elseif ($monthsDiff < 0) {
$monthsDiff += ($dayStart < $dayEnd ? 1 : 0);
}

$ascending = ($start <= $end);
$sign = $absolute || $ascending ? 1 : -1;
$monthsDiff = abs($monthsDiff);

if (!$ascending) {
[$start, $end] = [$end, $start];
}


$floorEnd = $start->avoidMutation()->addMonths($monthsDiff);

if ($floorEnd >= $end) {
return $sign * $monthsDiff;
}


$ceilEnd = $start->avoidMutation()->addMonths($monthsDiff + 1);

$daysToFloor = $floorEnd->diffInDays($end);
$daysToCeil = $end->diffInDays($ceilEnd);

return $sign * ($monthsDiff + $daysToFloor / ($daysToCeil + $daysToFloor));
}










public function diffInWeeks($date = null, bool $absolute = false, bool $utc = false): float
{
return $this->diffInDays($date, $absolute, $utc) / static::DAYS_PER_WEEK;
}










public function diffInDays($date = null, bool $absolute = false, bool $utc = false): float
{
$date = $this->resolveCarbon($date);
$current = $this;


if ($utc || ($date->timezoneName !== $current->timezoneName)) {
$date = $date->avoidMutation()->utc();
$current = $current->avoidMutation()->utc();
}

$negative = ($date < $current);
[$start, $end] = $negative ? [$date, $current] : [$current, $date];
$interval = $start->diffAsDateInterval($end);
$daysA = $this->getIntervalDayDiff($interval);
$floorEnd = $start->avoidMutation()->addDays($daysA);
$daysB = $daysA + ($floorEnd <= $end ? 1 : -1);
$ceilEnd = $start->avoidMutation()->addDays($daysB);
$microsecondsBetween = $floorEnd->diffInMicroseconds($ceilEnd);
$microsecondsToEnd = $floorEnd->diffInMicroseconds($end);

return ($negative && !$absolute ? -1 : 1)
* ($daysA * ($microsecondsBetween - $microsecondsToEnd) + $daysB * $microsecondsToEnd)
/ $microsecondsBetween;
}










public function diffInDaysFiltered(Closure $callback, $date = null, bool $absolute = false): int
{
return $this->diffFiltered(CarbonInterval::day(), $callback, $date, $absolute);
}










public function diffInHoursFiltered(Closure $callback, $date = null, bool $absolute = false): int
{
return $this->diffFiltered(CarbonInterval::hour(), $callback, $date, $absolute);
}











public function diffFiltered(CarbonInterval $ci, Closure $callback, $date = null, bool $absolute = false): int
{
$start = $this;
$end = $this->resolveCarbon($date);
$inverse = false;

if ($end < $start) {
$start = $end;
$end = $this;
$inverse = true;
}

$options = CarbonPeriod::EXCLUDE_END_DATE | ($this->isMutable() ? 0 : CarbonPeriod::IMMUTABLE);
$diff = $ci->toPeriod($start, $end, $options)->filter($callback)->count();

return $inverse && !$absolute ? -$diff : $diff;
}









public function diffInWeekdays($date = null, bool $absolute = false): int
{
return $this->diffInDaysFiltered(
static fn (CarbonInterface $date) => $date->isWeekday(),
$this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')),
$absolute,
);
}









public function diffInWeekendDays($date = null, bool $absolute = false): int
{
return $this->diffInDaysFiltered(
static fn (CarbonInterface $date) => $date->isWeekend(),
$this->resolveCarbon($date)->avoidMutation()->modify($this->format('H:i:s.u')),
$absolute,
);
}









public function diffInHours($date = null, bool $absolute = false): float
{
return $this->diffInMinutes($date, $absolute) / static::MINUTES_PER_HOUR;
}









public function diffInMinutes($date = null, bool $absolute = false): float
{
return $this->diffInSeconds($date, $absolute) / static::SECONDS_PER_MINUTE;
}









public function diffInSeconds($date = null, bool $absolute = false): float
{
return $this->diffInMilliseconds($date, $absolute) / static::MILLISECONDS_PER_SECOND;
}









public function diffInMicroseconds($date = null, bool $absolute = false): float
{

$date = $this->resolveCarbon($date);
$value = ($date->timestamp - $this->timestamp) * static::MICROSECONDS_PER_SECOND +
$date->micro - $this->micro;

return $absolute ? abs($value) : $value;
}









public function diffInMilliseconds($date = null, bool $absolute = false): float
{
return $this->diffInMicroseconds($date, $absolute) / static::MICROSECONDS_PER_MILLISECOND;
}






public function secondsSinceMidnight(): float
{
return $this->diffInSeconds($this->copy()->startOfDay(), true);
}






public function secondsUntilEndOfDay(): float
{
return $this->diffInSeconds($this->copy()->endOfDay(), true);
}



















































public function diffForHumans($other = null, $syntax = null, $short = false, $parts = 1, $options = null): string
{

if (\is_array($other)) {
$other['syntax'] = \array_key_exists('syntax', $other) ? $other['syntax'] : $syntax;
$syntax = $other;
$other = $syntax['other'] ?? null;
}

$intSyntax = &$syntax;

if (\is_array($syntax)) {
$syntax['syntax'] = $syntax['syntax'] ?? null;
$intSyntax = &$syntax['syntax'];
}

$intSyntax = (int) ($intSyntax ?? static::DIFF_RELATIVE_AUTO);
$intSyntax = $intSyntax === static::DIFF_RELATIVE_AUTO && $other === null ? static::DIFF_RELATIVE_TO_NOW : $intSyntax;

$parts = min(7, max(1, (int) $parts));
$skip = \is_array($syntax) ? ($syntax['skip'] ?? []) : [];
$options ??= $this->localHumanDiffOptions ?? $this->transmitFactory(
static fn () => static::getHumanDiffOptions(),
);

return $this->diff($other, skip: (array) $skip)->forHumans($syntax, (bool) $short, $parts, $options);
}

/**
@alias































*/
public function from($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
{
return $this->diffForHumans($other, $syntax, $short, $parts, $options);
}

/**
@alias



*/
public function since($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
{
return $this->diffForHumans($other, $syntax, $short, $parts, $options);
}

















































public function to($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
{
if (!$syntax && !$other) {
$syntax = CarbonInterface::DIFF_RELATIVE_TO_NOW;
}

return $this->resolveCarbon($other)->diffForHumans($this, $syntax, $short, $parts, $options);
}

/**
@alias































*/
public function until($other = null, $syntax = null, $short = false, $parts = 1, $options = null)
{
return $this->to($other, $syntax, $short, $parts, $options);
}





























public function fromNow($syntax = null, $short = false, $parts = 1, $options = null)
{
$other = null;

if ($syntax instanceof DateTimeInterface) {
[$other, $syntax, $short, $parts, $options] = array_pad(\func_get_args(), 5, null);
}

return $this->from($other, $syntax, $short, $parts, $options);
}





























public function toNow($syntax = null, $short = false, $parts = 1, $options = null)
{
return $this->to(null, $syntax, $short, $parts, $options);
}





























public function ago($syntax = null, $short = false, $parts = 1, $options = null)
{
$other = null;

if ($syntax instanceof DateTimeInterface) {
[$other, $syntax, $short, $parts, $options] = array_pad(\func_get_args(), 5, null);
}

return $this->from($other, $syntax, $short, $parts, $options);
}







public function timespan($other = null, $timezone = null): string
{
if (\is_string($other)) {
$other = $this->transmitFactory(static fn () => static::parse($other, $timezone));
}

return $this->diffForHumans($other, [
'join' => ', ',
'syntax' => CarbonInterface::DIFF_ABSOLUTE,
'parts' => INF,
]);
}












public function calendar($referenceTime = null, array $formats = [])
{

$current = $this->avoidMutation()->startOfDay();

$other = $this->resolveCarbon($referenceTime)->avoidMutation()->setTimezone($this->getTimezone())->startOfDay();
$diff = $other->diffInDays($current, false);
$format = $diff <= -static::DAYS_PER_WEEK ? 'sameElse' : (
$diff < -1 ? 'lastWeek' : (
$diff < 0 ? 'lastDay' : (
$diff < 1 ? 'sameDay' : (
$diff < 2 ? 'nextDay' : (
$diff < static::DAYS_PER_WEEK ? 'nextWeek' : 'sameElse'
)
)
)
)
);
$format = array_merge($this->getCalendarFormats(), $formats)[$format];
if ($format instanceof Closure) {
$format = $format($current, $other) ?? '';
}

return $this->isoFormat((string) $format);
}

private function getIntervalDayDiff(DateInterval $interval): int
{
return (int) $interval->format('%r%a');
}
}
