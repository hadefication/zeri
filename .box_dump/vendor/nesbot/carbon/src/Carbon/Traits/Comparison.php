<?php

declare(strict_types=1);










namespace Carbon\Traits;

use BackedEnum;
use BadMethodCallException;
use Carbon\CarbonConverterInterface;
use Carbon\CarbonInterface;
use Carbon\Exceptions\BadComparisonUnitException;
use Carbon\FactoryImmutable;
use Carbon\Month;
use Carbon\Unit;
use Carbon\WeekDay;
use Closure;
use DateInterval;
use DateTimeInterface;
use InvalidArgumentException;















trait Comparison
{
protected bool $endOfTime = false;

protected bool $startOfTime = false;













public function eq(DateTimeInterface|string $date): bool
{
return $this->equalTo($date);
}











public function equalTo(DateTimeInterface|string $date): bool
{
return $this == $this->resolveCarbon($date);
}













public function ne(DateTimeInterface|string $date): bool
{
return $this->notEqualTo($date);
}











public function notEqualTo(DateTimeInterface|string $date): bool
{
return !$this->equalTo($date);
}













public function gt(DateTimeInterface|string $date): bool
{
return $this->greaterThan($date);
}











public function greaterThan(DateTimeInterface|string $date): bool
{
return $this > $this->resolveCarbon($date);
}













public function isAfter(DateTimeInterface|string $date): bool
{
return $this->greaterThan($date);
}













public function gte(DateTimeInterface|string $date): bool
{
return $this->greaterThanOrEqualTo($date);
}











public function greaterThanOrEqualTo(DateTimeInterface|string $date): bool
{
return $this >= $this->resolveCarbon($date);
}













public function lt(DateTimeInterface|string $date): bool
{
return $this->lessThan($date);
}











public function lessThan(DateTimeInterface|string $date): bool
{
return $this < $this->resolveCarbon($date);
}













public function isBefore(DateTimeInterface|string $date): bool
{
return $this->lessThan($date);
}













public function lte(DateTimeInterface|string $date): bool
{
return $this->lessThanOrEqualTo($date);
}











public function lessThanOrEqualTo(DateTimeInterface|string $date): bool
{
return $this <= $this->resolveCarbon($date);
}


















public function between(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool
{
$date1 = $this->resolveCarbon($date1);
$date2 = $this->resolveCarbon($date2);

if ($date1->greaterThan($date2)) {
[$date1, $date2] = [$date2, $date1];
}

if ($equal) {
return $this >= $date1 && $this <= $date2;
}

return $this > $date1 && $this < $date2;
}











public function betweenIncluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool
{
return $this->between($date1, $date2, true);
}











public function betweenExcluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool
{
return $this->between($date1, $date2, false);
}














public function isBetween(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool
{
return $this->between($date1, $date2, $equal);
}










public function isWeekday(): bool
{
return !$this->isWeekend();
}










public function isWeekend(): bool
{
return \in_array(
$this->dayOfWeek,
$this->transmitFactory(static fn () => static::getWeekendDays()),
true,
);
}










public function isYesterday(): bool
{
return $this->toDateString() === $this->transmitFactory(
fn () => static::yesterday($this->getTimezone())->toDateString(),
);
}










public function isToday(): bool
{
return $this->toDateString() === $this->nowWithSameTz()->toDateString();
}










public function isTomorrow(): bool
{
return $this->toDateString() === $this->transmitFactory(
fn () => static::tomorrow($this->getTimezone())->toDateString(),
);
}










public function isFuture(): bool
{
return $this->greaterThan($this->nowWithSameTz());
}










public function isPast(): bool
{
return $this->lessThan($this->nowWithSameTz());
}











public function isNowOrFuture(): bool
{
return $this->greaterThanOrEqualTo($this->nowWithSameTz());
}











public function isNowOrPast(): bool
{
return $this->lessThanOrEqualTo($this->nowWithSameTz());
}










public function isLeapYear(): bool
{
return $this->rawFormat('L') === '1';
}

















public function isLongYear(): bool
{
return static::create($this->year, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === static::WEEKS_PER_YEAR + 1;
}















public function isLongIsoYear(): bool
{
return static::create($this->isoWeekYear, 12, 28, 0, 0, 0, $this->tz)->weekOfYear === 53;
}













public function isSameAs(string $format, DateTimeInterface|string $date): bool
{
return $this->rawFormat($format) === $this->resolveCarbon($date)->rawFormat($format);
}

















public function isSameUnit(string $unit, DateTimeInterface|string $date): bool
{
if ($unit ===  'quarter') {
$other = $this->resolveCarbon($date);

return $other->year === $this->year && $other->quarter === $this->quarter;
}

$units = [

'year' => 'Y',

'month' => 'Y-n',

'week' => 'o-W',

'day' => 'Y-m-d',

'hour' => 'Y-m-d H',

'minute' => 'Y-m-d H:i',

'second' => 'Y-m-d H:i:s',

'milli' => 'Y-m-d H:i:s.v',

'millisecond' => 'Y-m-d H:i:s.v',

'micro' => 'Y-m-d H:i:s.u',

'microsecond' => 'Y-m-d H:i:s.u',
];

if (isset($units[$unit])) {
return $this->isSameAs($units[$unit], $date);
}

if (isset($this->$unit)) {
return $this->resolveCarbon($date)->$unit === $this->$unit;
}

if ($this->isLocalStrictModeEnabled()) {
throw new BadComparisonUnitException($unit);
}

return false;
}














public function isCurrentUnit(string $unit): bool
{
return $this->{'isSame'.ucfirst($unit)}('now');
}

















public function isSameQuarter(DateTimeInterface|string $date, bool $ofSameYear = true): bool
{
$date = $this->resolveCarbon($date);

return $this->quarter === $date->quarter && (!$ofSameYear || $this->isSameYear($date));
}

















public function isSameMonth(DateTimeInterface|string $date, bool $ofSameYear = true): bool
{
return $this->isSameAs($ofSameYear ? 'Y-m' : 'm', $date);
}
















public function isDayOfWeek($dayOfWeek): bool
{
if (\is_string($dayOfWeek) && \defined($constant = static::class.'::'.strtoupper($dayOfWeek))) {
$dayOfWeek = \constant($constant);
}

return $this->dayOfWeek === $dayOfWeek;
}
















public function isBirthday(DateTimeInterface|string|null $date = null): bool
{
return $this->isSameAs('md', $date ?? 'now');
}













public function isLastOfMonth(): bool
{
return $this->day === $this->daysInMonth;
}










public function isStartOfUnit(
Unit $unit,
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
mixed ...$params,
): bool {
$interval ??= match ($unit) {
Unit::Day, Unit::Hour, Unit::Minute, Unit::Second, Unit::Millisecond, Unit::Microsecond => Unit::Microsecond,
default => Unit::Day,
};

$startOfUnit = $this->avoidMutation()->startOf($unit, ...$params);
$startOfUnitDateTime = $startOfUnit->rawFormat('Y-m-d H:i:s.u');
$maximumDateTime = $startOfUnit
->add($interval instanceof Unit ? '1  '.$interval->value : $interval)
->rawFormat('Y-m-d H:i:s.u');

if ($maximumDateTime < $startOfUnitDateTime) {
return false;
}

return $this->rawFormat('Y-m-d H:i:s.u') < $maximumDateTime;
}










public function isEndOfUnit(
Unit $unit,
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
mixed ...$params,
): bool {
$interval ??= match ($unit) {
Unit::Day, Unit::Hour, Unit::Minute, Unit::Second, Unit::Millisecond, Unit::Microsecond => Unit::Microsecond,
default => Unit::Day,
};

$endOfUnit = $this->avoidMutation()->endOf($unit, ...$params);
$endOfUnitDateTime = $endOfUnit->rawFormat('Y-m-d H:i:s.u');
$minimumDateTime = $endOfUnit
->sub($interval instanceof Unit ? '1  '.$interval->value : $interval)
->rawFormat('Y-m-d H:i:s.u');

if ($minimumDateTime > $endOfUnitDateTime) {
return false;
}

return $this->rawFormat('Y-m-d H:i:s.u') > $minimumDateTime;
}




public function isStartOfMillisecond(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Millisecond, $interval);
}




public function isEndOfMillisecond(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Millisecond, $interval);
}




public function isStartOfSecond(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Second, $interval);
}




public function isEndOfSecond(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Second, $interval);
}




public function isStartOfMinute(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Minute, $interval);
}




public function isEndOfMinute(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Minute, $interval);
}




public function isStartOfHour(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Hour, $interval);
}




public function isEndOfHour(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Hour, $interval);
}



















public function isStartOfDay(
Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false,
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
if ($checkMicroseconds === true) {
@trigger_error(
"Since 3.8.0, it's deprecated to use \$checkMicroseconds.\n".
"It will be removed in 4.0.0.\n".
"Instead, you should use either isStartOfDay(interval: Unit::Microsecond) or isStartOfDay(interval: Unit::Second)\n".
'And you can now use any custom interval as precision, such as isStartOfDay(interval: "15 minutes")',
\E_USER_DEPRECATED,
);
}

if ($interval === null && !\is_bool($checkMicroseconds)) {
$interval = $checkMicroseconds;
}

if ($interval !== null) {
if ($interval instanceof Unit) {
$interval = '1  '.$interval->value;
}

$date = $this->rawFormat('Y-m-d');
$time = $this->rawFormat('H:i:s.u');
$maximum = $this->avoidMutation()->startOfDay()->add($interval);
$maximumDate = $maximum->rawFormat('Y-m-d');

if ($date === $maximumDate) {
return $time < $maximum->rawFormat('H:i:s.u');
}

return $maximumDate > $date;
}


return $checkMicroseconds
? $this->rawFormat('H:i:s.u') === '00:00:00.000000'
: $this->rawFormat('H:i:s') === '00:00:00';
}





















public function isEndOfDay(
Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false,
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
if ($checkMicroseconds === true) {
@trigger_error(
"Since 3.8.0, it's deprecated to use \$checkMicroseconds.\n".
"It will be removed in 4.0.0.\n".
"Instead, you should use either isEndOfDay(interval: Unit::Microsecond) or isEndOfDay(interval: Unit::Second)\n".
'And you can now use any custom interval as precision, such as isEndOfDay(interval: "15 minutes")',
\E_USER_DEPRECATED,
);
}

if ($interval === null && !\is_bool($checkMicroseconds)) {
$interval = $checkMicroseconds;
}

if ($interval !== null) {
$date = $this->rawFormat('Y-m-d');
$time = $this->rawFormat('H:i:s.u');
$minimum = $this->avoidMutation()
->endOfDay()
->sub($interval instanceof Unit ? '1  '.$interval->value : $interval);
$minimumDate = $minimum->rawFormat('Y-m-d');

if ($date === $minimumDate) {
return $time > $minimum->rawFormat('H:i:s.u');
}

return $minimumDate < $date;
}


return $checkMicroseconds
? $this->rawFormat('H:i:s.u') === '23:59:59.999999'
: $this->rawFormat('H:i:s') === '23:59:59';
}










public function isStartOfWeek(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
WeekDay|int|null $weekStartsAt = null,
): bool {
return $this->isStartOfUnit(Unit::Week, $interval, $weekStartsAt);
}










public function isEndOfWeek(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
WeekDay|int|null $weekEndsAt = null,
): bool {
return $this->isEndOfUnit(Unit::Week, $interval, $weekEndsAt);
}




public function isStartOfMonth(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Month, $interval);
}




public function isEndOfMonth(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Month, $interval);
}




public function isStartOfQuarter(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Quarter, $interval);
}




public function isEndOfQuarter(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Quarter, $interval);
}




public function isStartOfYear(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Year, $interval);
}




public function isEndOfYear(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Year, $interval);
}




public function isStartOfDecade(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Decade, $interval);
}




public function isEndOfDecade(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Decade, $interval);
}




public function isStartOfCentury(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Century, $interval);
}




public function isEndOfCentury(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Century, $interval);
}




public function isStartOfMillennium(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isStartOfUnit(Unit::Millennium, $interval);
}




public function isEndOfMillennium(
Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null,
): bool {
return $this->isEndOfUnit(Unit::Millennium, $interval);
}











public function isMidnight(): bool
{
return $this->isStartOfDay();
}












public function isMidday(): bool
{

return $this->rawFormat('G:i:s') === static::$midDayAt.':00:00';
}










public static function hasFormat(string $date, string $format): bool
{
return FactoryImmutable::getInstance()->hasFormat($date, $format);
}















public static function hasFormatWithModifiers(?string $date, string $format): bool
{
return FactoryImmutable::getInstance()->hasFormatWithModifiers($date, $format);
}











public static function canBeCreatedFromFormat(?string $date, string $format): bool
{
if ($date === null) {
return false;
}

try {


if (!static::rawCreateFromFormat($format, $date)) {
return false;
}
} catch (InvalidArgumentException) {
return false;
}

return static::hasFormatWithModifiers($date, $format);
}























public function is(WeekDay|Month|string $tester): bool
{
if ($tester instanceof BackedEnum) {
$tester = $tester->name;
}

$tester = trim($tester);

if (preg_match('/^\d+$/', $tester)) {
return $this->year === (int) $tester;
}

if (preg_match('/^(?:Jan|January|Feb|February|Mar|March|Apr|April|May|Jun|June|Jul|July|Aug|August|Sep|September|Oct|October|Nov|November|Dec|December)$/i', $tester)) {
return $this->isSameMonth(
$this->transmitFactory(static fn () => static::parse("$tester 1st")),
false,
);
}

if (preg_match('/^\d{3,}-\d{1,2}$/', $tester)) {
return $this->isSameMonth(
$this->transmitFactory(static fn () => static::parse($tester)),
);
}

if (preg_match('/^(\d{1,2})-(\d{1,2})$/', $tester, $match)) {
return $this->month === (int) $match[1] && $this->day === (int) $match[2];
}

$modifier = preg_replace('/(\d)h$/i', '$1:00', $tester);


$median = $this->transmitFactory(static fn () => static::parse('5555-06-15 12:30:30.555555'))
->modify($modifier);
$current = $this->avoidMutation();

$other = $this->avoidMutation()->modify($modifier);

if ($current->eq($other)) {
return true;
}

if (preg_match('/\d:\d{1,2}:\d{1,2}$/', $tester)) {
return $current->startOfSecond()->eq($other);
}

if (preg_match('/\d:\d{1,2}$/', $tester)) {
return $current->startOfMinute()->eq($other);
}

if (preg_match('/\d(?:h|am|pm)$/', $tester)) {
return $current->startOfHour()->eq($other);
}

if (preg_match(
'/^(?:january|february|march|april|may|june|july|august|september|october|november|december)(?:\s+\d+)?$/i',
$tester,
)) {
return $current->startOfMonth()->eq($other->startOfMonth());
}

$units = [
'month' => [1, 'year'],
'day' => [1, 'month'],
'hour' => [0, 'day'],
'minute' => [0, 'hour'],
'second' => [0, 'minute'],
'microsecond' => [0, 'second'],
];

foreach ($units as $unit => [$minimum, $startUnit]) {
if ($minimum === $median->$unit) {
$current = $current->startOf($startUnit);

break;
}
}

return $current->eq($other);
}






public function isStartOfTime(): bool
{
return $this->startOfTime ?? false;
}






public function isEndOfTime(): bool
{
return $this->endOfTime ?? false;
}
}
