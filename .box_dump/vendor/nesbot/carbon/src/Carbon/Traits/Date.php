<?php

declare(strict_types=1);










namespace Carbon\Traits;

use BadMethodCallException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Carbon\Exceptions\BadComparisonUnitException;
use Carbon\Exceptions\ImmutableException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Carbon\Exceptions\UnitException;
use Carbon\Exceptions\UnknownGetterException;
use Carbon\Exceptions\UnknownMethodException;
use Carbon\Exceptions\UnknownSetterException;
use Carbon\Exceptions\UnknownUnitException;
use Carbon\FactoryImmutable;
use Carbon\Month;
use Carbon\Translator;
use Carbon\Unit;
use Carbon\WeekDay;
use Closure;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use InvalidArgumentException;
use ReflectionException;
use Symfony\Component\Clock\NativeClock;
use Throwable;

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
trait Date
{
use Boundaries;
use Comparison;
use Converter;
use Creator;
use Difference;
use Macro;
use MagicParameter;
use Modifiers;
use Mutability;
use ObjectInitialisation;
use Options;
use Rounding;
use Serialization;
use Test;
use Timestamp;
use Units;
use Week;






protected static $days = [

CarbonInterface::SUNDAY => 'Sunday',

CarbonInterface::MONDAY => 'Monday',

CarbonInterface::TUESDAY => 'Tuesday',

CarbonInterface::WEDNESDAY => 'Wednesday',

CarbonInterface::THURSDAY => 'Thursday',

CarbonInterface::FRIDAY => 'Friday',

CarbonInterface::SATURDAY => 'Saturday',
];






protected static $units = [


'year',


'month',


'day',


'hour',


'minute',


'second',


'milli',


'millisecond',


'micro',


'microsecond',
];











protected static function safeCreateDateTimeZone(
DateTimeZone|string|int|false|null $object,
DateTimeZone|string|int|false|null $objectDump = null,
): ?CarbonTimeZone {
return CarbonTimeZone::instance($object, $objectDump);
}






public function getTimezone(): CarbonTimeZone
{
return $this->transmitFactory(fn () => CarbonTimeZone::instance(parent::getTimezone()));
}




protected static function getRangesByUnit(int $daysInMonth = 31): array
{
return [

'year' => [1, 9999],

'month' => [1, static::MONTHS_PER_YEAR],

'day' => [1, $daysInMonth],

'hour' => [0, static::HOURS_PER_DAY - 1],

'minute' => [0, static::MINUTES_PER_HOUR - 1],

'second' => [0, static::SECONDS_PER_MINUTE - 1],
];
}






public function copy()
{
return clone $this;
}

/**
@alias




*/
public function clone()
{
return clone $this;
}









public function avoidMutation(): static
{
if ($this instanceof DateTimeImmutable) {
return $this;
}

return clone $this;
}






public function nowWithSameTz(): static
{
$timezone = $this->getTimezone();

return $this->getClock()?->nowAs(static::class, $timezone) ?? static::now($timezone);
}









public function carbonize($date = null)
{
if ($date instanceof DateInterval) {
return $this->avoidMutation()->add($date);
}

if ($date instanceof DatePeriod || $date instanceof CarbonPeriod) {
$date = $date->getStartDate();
}

return $this->resolveCarbon($date);
}












public function __get(string $name): mixed
{
return $this->get($name);
}








public function get(Unit|string $name): mixed
{
static $localizedFormats = [

'localeDayOfWeek' => 'dddd',

'shortLocaleDayOfWeek' => 'ddd',

'localeMonth' => 'MMMM',

'shortLocaleMonth' => 'MMM',
];

$name = Unit::toName($name);

if (isset($localizedFormats[$name])) {
return $this->isoFormat($localizedFormats[$name]);
}

static $formats = [

'year' => 'Y',

'yearIso' => 'o',



'month' => 'n',

'day' => 'j',

'hour' => 'G',

'minute' => 'i',

'second' => 's',

'micro' => 'u',

'microsecond' => 'u',

'dayOfWeek' => 'w',

'dayOfWeekIso' => 'N',

'weekOfYear' => 'W',

'daysInMonth' => 't',

'timestamp' => 'U',

'latinMeridiem' => 'a',

'latinUpperMeridiem' => 'A',

'englishDayOfWeek' => 'l',

'shortEnglishDayOfWeek' => 'D',

'englishMonth' => 'F',

'shortEnglishMonth' => 'M',

'timezoneAbbreviatedName' => 'T',

'tzAbbrName' => 'T',
];

switch (true) {
case isset($formats[$name]):
$value = $this->rawFormat($formats[$name]);

return is_numeric($value) ? (int) $value : $value;


case $name === 'dayName':
return $this->getTranslatedDayName();

case $name === 'shortDayName':
return $this->getTranslatedShortDayName();

case $name === 'minDayName':
return $this->getTranslatedMinDayName();

case $name === 'monthName':
return $this->getTranslatedMonthName();

case $name === 'shortMonthName':
return $this->getTranslatedShortMonthName();

case $name === 'meridiem':
return $this->meridiem(true);

case $name === 'upperMeridiem':
return $this->meridiem();

case $name === 'noZeroHour':
return $this->hour ?: 24;

case $name === 'milliseconds':

case $name === 'millisecond':

case $name === 'milli':
return (int) floor(((int) $this->rawFormat('u')) / 1000);


case $name === 'week':
return (int) $this->week();


case $name === 'isoWeek':
return (int) $this->isoWeek();


case $name === 'weekYear':
return (int) $this->weekYear();


case $name === 'isoWeekYear':
return (int) $this->isoWeekYear();


case $name === 'weeksInYear':
return $this->weeksInYear();


case $name === 'isoWeeksInYear':
return $this->isoWeeksInYear();


case $name === 'weekOfMonth':
return (int) ceil($this->day / static::DAYS_PER_WEEK);


case $name === 'weekNumberInMonth':
return (int) ceil(($this->day + $this->avoidMutation()->startOfMonth()->dayOfWeekIso - 1) / static::DAYS_PER_WEEK);


case $name === 'firstWeekDay':
return (int) $this->getTranslationMessage('first_day_of_week');


case $name === 'lastWeekDay':
return $this->transmitFactory(fn () => static::weekRotate((int) $this->getTranslationMessage('first_day_of_week'), -1));


case $name === 'dayOfYear':
return 1 + (int) ($this->rawFormat('z'));


case $name === 'daysInYear':
return static::DAYS_PER_YEAR + ($this->isLeapYear() ? 1 : 0);


case $name === 'age':
return (int) $this->diffInYears();


case $name === 'quarter':
return (int) ceil($this->month / static::MONTHS_PER_QUARTER);



case $name === 'decade':
return (int) ceil($this->year / static::YEARS_PER_DECADE);



case $name === 'century':
$factor = 1;
$year = $this->year;

if ($year < 0) {
$year = -$year;
$factor = -1;
}

return (int) ($factor * ceil($year / static::YEARS_PER_CENTURY));



case $name === 'millennium':
$factor = 1;
$year = $this->year;

if ($year < 0) {
$year = -$year;
$factor = -1;
}

return (int) ($factor * ceil($year / static::YEARS_PER_MILLENNIUM));


case $name === 'offset':
return $this->getOffset();


case $name === 'offsetMinutes':
return $this->getOffset() / static::SECONDS_PER_MINUTE;


case $name === 'offsetHours':
return $this->getOffset() / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR;


case $name === 'dst':
return $this->rawFormat('I') === '1';


case $name === 'local':
return $this->getOffset() === $this->avoidMutation()->setTimezone(date_default_timezone_get())->getOffset();


case $name === 'utc':
return $this->getOffset() === 0;







case $name === 'timezone' || $name === 'tz':
return $this->getTimezone();



case $name === 'timezoneName' || $name === 'tzName':
return $this->getTimezone()->getName();


case $name === 'locale':
return $this->getTranslatorLocale();

case preg_match('/^([a-z]{2,})(In|Of)([A-Z][a-z]+)$/', $name, $match):
[, $firstUnit, $operator, $secondUnit] = $match;

try {
$start = $this->avoidMutation()->startOf($secondUnit);
$value = $operator === 'Of'
? (\in_array($firstUnit, [

'day',
'week',
'month',
'quarter',
], true) ? 1 : 0) + floor($start->diffInUnit($firstUnit, $this))
: round($start->diffInUnit($firstUnit, $start->avoidMutation()->add($secondUnit, 1)));

return (int) $value;
} catch (UnknownUnitException) {

}

default:
$macro = $this->getLocalMacro('get'.ucfirst($name));

if ($macro) {
return $this->executeCallableWithContext($macro);
}

throw new UnknownGetterException($name);
}
}








public function __isset($name)
{
try {
$this->__get($name);
} catch (UnknownGetterException | ReflectionException) {
return false;
}

return true;
}











public function __set($name, $value)
{
if ($this->constructedObjectId === spl_object_hash($this)) {
$this->set($name, $value);

return;
}

$this->$name = $value;
}








public function set(Unit|array|string $name, DateTimeZone|Month|string|int|float|null $value = null): static
{
if ($this->isImmutable()) {
throw new ImmutableException(\sprintf('%s class', static::class));
}

if (\is_array($name)) {
foreach ($name as $key => $value) {
$this->set($key, $value);
}

return $this;
}

$name = Unit::toName($name);

switch ($name) {
case 'milliseconds':
case 'millisecond':
case 'milli':
case 'microseconds':
case 'microsecond':
case 'micro':
if (str_starts_with($name, 'milli')) {
$value *= 1000;
}

while ($value < 0) {
$this->subSecond();
$value += static::MICROSECONDS_PER_SECOND;
}

while ($value >= static::MICROSECONDS_PER_SECOND) {
$this->addSecond();
$value -= static::MICROSECONDS_PER_SECOND;
}

$this->modify($this->rawFormat('H:i:s.').str_pad((string) round($value), 6, '0', STR_PAD_LEFT));

break;

case 'year':
case 'month':
case 'day':
case 'hour':
case 'minute':
case 'second':
[$year, $month, $day, $hour, $minute, $second] = array_map('intval', explode('-', $this->rawFormat('Y-n-j-G-i-s')));
$$name = self::monthToInt($value, $name);
$this->setDateTime($year, $month, $day, $hour, $minute, $second);

break;

case 'week':
$this->week($value);

break;

case 'isoWeek':
$this->isoWeek($value);

break;

case 'weekYear':
$this->weekYear($value);

break;

case 'isoWeekYear':
$this->isoWeekYear($value);

break;

case 'dayOfYear':
$this->addDays($value - $this->dayOfYear);

break;

case 'dayOfWeek':
$this->addDays($value - $this->dayOfWeek);

break;

case 'dayOfWeekIso':
$this->addDays($value - $this->dayOfWeekIso);

break;

case 'timestamp':
$this->setTimestamp($value);

break;

case 'offset':
$this->setTimezone(static::safeCreateDateTimeZone($value / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR));

break;

case 'offsetMinutes':
$this->setTimezone(static::safeCreateDateTimeZone($value / static::MINUTES_PER_HOUR));

break;

case 'offsetHours':
$this->setTimezone(static::safeCreateDateTimeZone($value));

break;

case 'timezone':
case 'tz':
$this->setTimezone($value);

break;

default:
if (preg_match('/^([a-z]{2,})Of([A-Z][a-z]+)$/', $name, $match)) {
[, $firstUnit, $secondUnit] = $match;

try {
$start = $this->avoidMutation()->startOf($secondUnit);
$currentValue = (\in_array($firstUnit, [

'day',
'week',
'month',
'quarter',
], true) ? 1 : 0) + (int) floor($start->diffInUnit($firstUnit, $this));


if (!\is_int($value)) {
throw new UnitException("->$name expects integer value");
}

$this->addUnit($firstUnit, $value - $currentValue);

break;
} catch (UnknownUnitException) {

}
}

$macro = $this->getLocalMacro('set'.ucfirst($name));

if ($macro) {
$this->executeCallableWithContext($macro, $value);

break;
}

if ($this->isLocalStrictModeEnabled()) {
throw new UnknownSetterException($name);
}

$this->$name = $value;
}

return $this;
}








public function getTranslatedDayName(
?string $context = null,
string $keySuffix = '',
?string $defaultValue = null,
): string {
return $this->getTranslatedFormByRegExp('weekdays', $keySuffix, $context, $this->dayOfWeek, $defaultValue ?: $this->englishDayOfWeek);
}






public function getTranslatedShortDayName(?string $context = null): string
{
return $this->getTranslatedDayName($context, '_short', $this->shortEnglishDayOfWeek);
}






public function getTranslatedMinDayName(?string $context = null): string
{
return $this->getTranslatedDayName($context, '_min', $this->shortEnglishDayOfWeek);
}








public function getTranslatedMonthName(
?string $context = null,
string $keySuffix = '',
?string $defaultValue = null,
): string {
return $this->getTranslatedFormByRegExp('months', $keySuffix, $context, $this->month - 1, $defaultValue ?: $this->englishMonth);
}






public function getTranslatedShortMonthName(?string $context = null): string
{
return $this->getTranslatedMonthName($context, '_short', $this->shortEnglishMonth);
}

/**
@template
@psalm-param
@psalm-return(T is int ? static : int)








*/
public function dayOfYear(?int $value = null): static|int
{
$dayOfYear = $this->dayOfYear;

return $value === null ? $dayOfYear : $this->addDays($value - $dayOfYear);
}






public function weekday(WeekDay|int|null $value = null): static|int
{
if ($value === null) {
return $this->dayOfWeek;
}

$firstDay = (int) ($this->getTranslationMessage('first_day_of_week') ?? 0);
$dayOfWeek = ($this->dayOfWeek + 7 - $firstDay) % 7;

return $this->addDays(((WeekDay::int($value) + 7 - $firstDay) % 7) - $dayOfWeek);
}






public function isoWeekday(WeekDay|int|null $value = null): static|int
{
$dayOfWeekIso = $this->dayOfWeekIso;

return $value === null ? $dayOfWeekIso : $this->addDays(WeekDay::int($value) - $dayOfWeekIso);
}









public function getDaysFromStartOfWeek(WeekDay|int|null $weekStartsAt = null): int
{
$firstDay = (int) (WeekDay::int($weekStartsAt) ?? $this->getTranslationMessage('first_day_of_week') ?? 0);

return ($this->dayOfWeek + 7 - $firstDay) % 7;
}










public function setDaysFromStartOfWeek(int $numberOfDays, WeekDay|int|null $weekStartsAt = null): static
{
return $this->addDays($numberOfDays - $this->getDaysFromStartOfWeek(WeekDay::int($weekStartsAt)));
}








public function setUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static
{
try {
$start = $this->avoidMutation()->startOf($overflowUnit);
$end = $this->avoidMutation()->endOf($overflowUnit);

$date = $this->$valueUnit($value);

if ($date < $start) {
return $date->mutateIfMutable($start);
}

if ($date > $end) {
return $date->mutateIfMutable($end);
}

return $date;
} catch (BadMethodCallException | ReflectionException $exception) {
throw new UnknownUnitException($valueUnit, 0, $exception);
}
}








public function addUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static
{
return $this->setUnitNoOverflow($valueUnit, $this->$valueUnit + $value, $overflowUnit);
}








public function subUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static
{
return $this->setUnitNoOverflow($valueUnit, $this->$valueUnit - $value, $overflowUnit);
}




public function utcOffset(?int $minuteOffset = null): static|int
{
if ($minuteOffset === null) {
return $this->offsetMinutes;
}

return $this->setTimezone(CarbonTimeZone::createFromMinuteOffset($minuteOffset));
}






public function setDate(int $year, int $month, int $day): static
{
return parent::setDate($year, $month, $day);
}






public function setISODate(int $year, int $week, int $day = 1): static
{
return parent::setISODate($year, $week, $day);
}




public function setDateTime(
int $year,
int $month,
int $day,
int $hour,
int $minute,
int $second = 0,
int $microseconds = 0,
): static {
return $this->setDate($year, $month, $day)->setTime($hour, $minute, $second, $microseconds);
}






public function setTime(int $hour, int $minute, int $second = 0, int $microseconds = 0): static
{
return parent::setTime($hour, $minute, $second, $microseconds);
}






public function setTimestamp(float|int|string $timestamp): static
{
[$seconds, $microseconds] = self::getIntegerAndDecimalParts($timestamp);

return parent::setTimestamp((int) $seconds)->setMicroseconds((int) $microseconds);
}




public function setTimeFromTimeString(string $time): static
{
if (!str_contains($time, ':')) {
$time .= ':0';
}

return $this->modify($time);
}

/**
@alias
*/
public function timezone(DateTimeZone|string|int $value): static
{
return $this->setTimezone($value);
}






public function tz(DateTimeZone|string|int|null $value = null): static|string
{
if ($value === null) {
return $this->tzName;
}

return $this->setTimezone($value);
}




public function setTimezone(DateTimeZone|string|int $timeZone): static
{
return parent::setTimezone(static::safeCreateDateTimeZone($timeZone));
}




public function shiftTimezone(DateTimeZone|string $value): static
{
$dateTimeString = $this->format('Y-m-d H:i:s.u');

return $this
->setTimezone($value)
->modify($dateTimeString);
}




public function utc(): static
{
return $this->setTimezone('UTC');
}




public function setDateFrom(DateTimeInterface|string $date): static
{
$date = $this->resolveCarbon($date);

return $this->setDate($date->year, $date->month, $date->day);
}




public function setTimeFrom(DateTimeInterface|string $date): static
{
$date = $this->resolveCarbon($date);

return $this->setTime($date->hour, $date->minute, $date->second, $date->microsecond);
}




public function setDateTimeFrom(DateTimeInterface|string $date): static
{
$date = $this->resolveCarbon($date);

return $this->modify($date->rawFormat('Y-m-d H:i:s.u'));
}




public static function getDays(): array
{
return static::$days;
}










public static function getWeekStartsAt(?string $locale = null): int
{
return (int) static::getTranslationMessageWith(
$locale ? Translator::get($locale) : static::getTranslator(),
'first_day_of_week',
);
}








public static function getWeekEndsAt(?string $locale = null): int
{
return static::weekRotate(static::getWeekStartsAt($locale), -1);
}




public static function getWeekendDays(): array
{
return FactoryImmutable::getInstance()->getWeekendDays();
}
























public static function setWeekendDays(array $days): void
{
FactoryImmutable::getDefaultInstance()->setWeekendDays($days);
}






public static function hasRelativeKeywords(?string $time): bool
{
if (!$time || strtotime($time) === false) {
return false;
}

$date1 = new DateTime('2000-01-01T00:00:00Z');
$date1->modify($time);
$date2 = new DateTime('2001-12-25T00:00:00Z');
$date2->modify($time);

return $date1 != $date2;
}










public function getIsoFormats(?string $locale = null): array
{
return [
'LT' => $this->getTranslationMessage('formats.LT', $locale),
'LTS' => $this->getTranslationMessage('formats.LTS', $locale),
'L' => $this->getTranslationMessage('formats.L', $locale),
'LL' => $this->getTranslationMessage('formats.LL', $locale),
'LLL' => $this->getTranslationMessage('formats.LLL', $locale),
'LLLL' => $this->getTranslationMessage('formats.LLLL', $locale),
'l' => $this->getTranslationMessage('formats.l', $locale),
'll' => $this->getTranslationMessage('formats.ll', $locale),
'lll' => $this->getTranslationMessage('formats.lll', $locale),
'llll' => $this->getTranslationMessage('formats.llll', $locale),
];
}






public function getCalendarFormats(?string $locale = null): array
{
return [
'sameDay' => $this->getTranslationMessage('calendar.sameDay', $locale, '[Today at] LT'),
'nextDay' => $this->getTranslationMessage('calendar.nextDay', $locale, '[Tomorrow at] LT'),
'nextWeek' => $this->getTranslationMessage('calendar.nextWeek', $locale, 'dddd [at] LT'),
'lastDay' => $this->getTranslationMessage('calendar.lastDay', $locale, '[Yesterday at] LT'),
'lastWeek' => $this->getTranslationMessage('calendar.lastWeek', $locale, '[Last] dddd [at] LT'),
'sameElse' => $this->getTranslationMessage('calendar.sameElse', $locale, 'L'),
];
}




public static function getIsoUnits(): array
{
static $units = null;

$units ??= [
'OD' => ['getAltNumber', ['day']],
'OM' => ['getAltNumber', ['month']],
'OY' => ['getAltNumber', ['year']],
'OH' => ['getAltNumber', ['hour']],
'Oh' => ['getAltNumber', ['h']],
'Om' => ['getAltNumber', ['minute']],
'Os' => ['getAltNumber', ['second']],
'D' => 'day',
'DD' => ['rawFormat', ['d']],
'Do' => ['ordinal', ['day', 'D']],
'd' => 'dayOfWeek',
'dd' => static fn (CarbonInterface $date, $originalFormat = null) => $date->getTranslatedMinDayName(
$originalFormat,
),
'ddd' => static fn (CarbonInterface $date, $originalFormat = null) => $date->getTranslatedShortDayName(
$originalFormat,
),
'dddd' => static fn (CarbonInterface $date, $originalFormat = null) => $date->getTranslatedDayName(
$originalFormat,
),
'DDD' => 'dayOfYear',
'DDDD' => ['getPaddedUnit', ['dayOfYear', 3]],
'DDDo' => ['ordinal', ['dayOfYear', 'DDD']],
'e' => ['weekday', []],
'E' => 'dayOfWeekIso',
'H' => ['rawFormat', ['G']],
'HH' => ['rawFormat', ['H']],
'h' => ['rawFormat', ['g']],
'hh' => ['rawFormat', ['h']],
'k' => 'noZeroHour',
'kk' => ['getPaddedUnit', ['noZeroHour']],
'hmm' => ['rawFormat', ['gi']],
'hmmss' => ['rawFormat', ['gis']],
'Hmm' => ['rawFormat', ['Gi']],
'Hmmss' => ['rawFormat', ['Gis']],
'm' => 'minute',
'mm' => ['rawFormat', ['i']],
'a' => 'meridiem',
'A' => 'upperMeridiem',
's' => 'second',
'ss' => ['getPaddedUnit', ['second']],
'S' => static fn (CarbonInterface $date) => (string) floor($date->micro / 100000),
'SS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro / 10000, 2),
'SSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro / 1000, 3),
'SSSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro / 100, 4),
'SSSSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro / 10, 5),
'SSSSSS' => ['getPaddedUnit', ['micro', 6]],
'SSSSSSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro * 10, 7),
'SSSSSSSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro * 100, 8),
'SSSSSSSSS' => static fn (CarbonInterface $date) => self::floorZeroPad($date->micro * 1000, 9),
'M' => 'month',
'MM' => ['rawFormat', ['m']],
'MMM' => static function (CarbonInterface $date, $originalFormat = null) {
$month = $date->getTranslatedShortMonthName($originalFormat);
$suffix = $date->getTranslationMessage('mmm_suffix');
if ($suffix && $month !== $date->monthName) {
$month .= $suffix;
}

return $month;
},
'MMMM' => static fn (CarbonInterface $date, $originalFormat = null) => $date->getTranslatedMonthName(
$originalFormat,
),
'Mo' => ['ordinal', ['month', 'M']],
'Q' => 'quarter',
'Qo' => ['ordinal', ['quarter', 'M']],
'G' => 'isoWeekYear',
'GG' => ['getPaddedUnit', ['isoWeekYear']],
'GGG' => ['getPaddedUnit', ['isoWeekYear', 3]],
'GGGG' => ['getPaddedUnit', ['isoWeekYear', 4]],
'GGGGG' => ['getPaddedUnit', ['isoWeekYear', 5]],
'g' => 'weekYear',
'gg' => ['getPaddedUnit', ['weekYear']],
'ggg' => ['getPaddedUnit', ['weekYear', 3]],
'gggg' => ['getPaddedUnit', ['weekYear', 4]],
'ggggg' => ['getPaddedUnit', ['weekYear', 5]],
'W' => 'isoWeek',
'WW' => ['getPaddedUnit', ['isoWeek']],
'Wo' => ['ordinal', ['isoWeek', 'W']],
'w' => 'week',
'ww' => ['getPaddedUnit', ['week']],
'wo' => ['ordinal', ['week', 'w']],
'x' => ['valueOf', []],
'X' => 'timestamp',
'Y' => 'year',
'YY' => ['rawFormat', ['y']],
'YYYY' => ['getPaddedUnit', ['year', 4]],
'YYYYY' => ['getPaddedUnit', ['year', 5]],
'YYYYYY' => static fn (CarbonInterface $date) => ($date->year < 0 ? '' : '+').
$date->getPaddedUnit('year', 6),
'z' => ['rawFormat', ['T']],
'zz' => 'tzName',
'Z' => ['getOffsetString', []],
'ZZ' => ['getOffsetString', ['']],
];

return $units;
}









public function getPaddedUnit($unit, $length = 2, $padString = '0', $padType = STR_PAD_LEFT): string
{
return ($this->$unit < 0 ? '-' : '').str_pad((string) abs($this->$unit), $length, $padString, $padType);
}




public function ordinal(string $key, ?string $period = null): string
{
$number = $this->$key;
$result = $this->translate('ordinal', [
':number' => $number,
':period' => (string) $period,
]);

return (string) ($result === 'ordinal' ? $number : $result);
}






public function meridiem(bool $isLower = false): string
{
$hour = $this->hour;
$index = $hour < static::HOURS_PER_DAY / 2 ? 0 : 1;

if ($isLower) {
$key = 'meridiem.'.($index + 2);
$result = $this->translate($key);

if ($result !== $key) {
return $result;
}
}

$key = "meridiem.$index";
$result = $this->translate($key);
if ($result === $key) {
$result = $this->translate('meridiem', [
':hour' => $this->hour,
':minute' => $this->minute,
':isLower' => $isLower,
]);

if ($result === 'meridiem') {
return $isLower ? $this->latinMeridiem : $this->latinUpperMeridiem;
}
} elseif ($isLower) {
$result = mb_strtolower($result);
}

return $result;
}






public function getAltNumber(string $key): string
{
return $this->translateNumber((int) (\strlen($key) > 1 ? $this->$key : $this->rawFormat($key)));
}






public function isoFormat(string $format, ?string $originalFormat = null): string
{
$result = '';
$length = mb_strlen($format);
$originalFormat ??= $format;
$inEscaped = false;
$formats = null;
$units = null;

for ($i = 0; $i < $length; $i++) {
$char = mb_substr($format, $i, 1);

if ($char === '\\') {
$result .= mb_substr($format, ++$i, 1);

continue;
}

if ($char === '[' && !$inEscaped) {
$inEscaped = true;

continue;
}

if ($char === ']' && $inEscaped) {
$inEscaped = false;

continue;
}

if ($inEscaped) {
$result .= $char;

continue;
}

$input = mb_substr($format, $i);

if (preg_match('/^(LTS|LT|l{1,4}|L{1,4})/', $input, $match)) {
if ($formats === null) {
$formats = $this->getIsoFormats();
}

$code = $match[0];
$sequence = $formats[$code] ?? preg_replace_callback(
'/MMMM|MM|DD|dddd/',
static fn ($code) => mb_substr($code[0], 1),
$formats[strtoupper($code)] ?? '',
);
$rest = mb_substr($format, $i + mb_strlen($code));
$format = mb_substr($format, 0, $i).$sequence.$rest;
$length = mb_strlen($format);
$input = $sequence.$rest;
}

if (preg_match('/^'.CarbonInterface::ISO_FORMAT_REGEXP.'/', $input, $match)) {
$code = $match[0];

if ($units === null) {
$units = static::getIsoUnits();
}

$sequence = $units[$code] ?? '';

if ($sequence instanceof Closure) {
$sequence = $sequence($this, $originalFormat);
} elseif (\is_array($sequence)) {
try {
$sequence = $this->{$sequence[0]}(...$sequence[1]);
} catch (ReflectionException | InvalidArgumentException | BadMethodCallException) {
$sequence = '';
}
} elseif (\is_string($sequence)) {
$sequence = $this->$sequence ?? $code;
}

$format = mb_substr($format, 0, $i).$sequence.mb_substr($format, $i + mb_strlen($code));
$i += mb_strlen((string) $sequence) - 1;
$length = mb_strlen($format);
$char = $sequence;
}

$result .= $char;
}

return $result;
}




public static function getFormatsToIsoReplacements(): array
{
static $replacements = null;

$replacements ??= [
'd' => true,
'D' => 'ddd',
'j' => true,
'l' => 'dddd',
'N' => true,
'S' => static fn ($date) => str_replace((string) $date->rawFormat('j'), '', $date->isoFormat('Do')),
'w' => true,
'z' => true,
'W' => true,
'F' => 'MMMM',
'm' => true,
'M' => 'MMM',
'n' => true,
't' => true,
'L' => true,
'o' => true,
'Y' => true,
'y' => true,
'a' => 'a',
'A' => 'A',
'B' => true,
'g' => true,
'G' => true,
'h' => true,
'H' => true,
'i' => true,
's' => true,
'u' => true,
'v' => true,
'E' => true,
'I' => true,
'O' => true,
'P' => true,
'Z' => true,
'c' => true,
'r' => true,
'U' => true,
'T' => true,
];

return $replacements;
}





public function translatedFormat(string $format): string
{
$replacements = static::getFormatsToIsoReplacements();
$context = '';
$isoFormat = '';
$length = mb_strlen($format);

for ($i = 0; $i < $length; $i++) {
$char = mb_substr($format, $i, 1);

if ($char === '\\') {
$replacement = mb_substr($format, $i, 2);
$isoFormat .= $replacement;
$i++;

continue;
}

if (!isset($replacements[$char])) {
$replacement = preg_match('/^[A-Za-z]$/', $char) ? "\\$char" : $char;
$isoFormat .= $replacement;
$context .= $replacement;

continue;
}

$replacement = $replacements[$char];

if ($replacement === true) {
static $contextReplacements = null;

if ($contextReplacements === null) {
$contextReplacements = [
'm' => 'MM',
'd' => 'DD',
't' => 'D',
'j' => 'D',
'N' => 'e',
'w' => 'e',
'n' => 'M',
'o' => 'YYYY',
'Y' => 'YYYY',
'y' => 'YY',
'g' => 'h',
'G' => 'H',
'h' => 'hh',
'H' => 'HH',
'i' => 'mm',
's' => 'ss',
];
}

$isoFormat .= '['.$this->rawFormat($char).']';
$context .= $contextReplacements[$char] ?? ' ';

continue;
}

if ($replacement instanceof Closure) {
$replacement = '['.$replacement($this).']';
$isoFormat .= $replacement;
$context .= $replacement;

continue;
}

$isoFormat .= $replacement;
$context .= $replacement;
}

return $this->isoFormat($isoFormat, $context);
}









public function getOffsetString(string $separator = ':'): string
{
$second = $this->getOffset();
$symbol = $second < 0 ? '-' : '+';
$minute = abs($second) / static::SECONDS_PER_MINUTE;
$hour = self::floorZeroPad($minute / static::MINUTES_PER_HOUR, 2);
$minute = self::floorZeroPad(((int) $minute) % static::MINUTES_PER_HOUR, 2);

return "$symbol$hour$separator$minute";
}









public static function __callStatic(string $method, array $parameters): mixed
{
if (!static::hasMacro($method)) {
foreach (static::getGenericMacros() as $callback) {
try {
return static::executeStaticCallable($callback, $method, ...$parameters);
} catch (BadMethodCallException) {
continue;
}
}

if (static::isStrictModeEnabled()) {
throw new UnknownMethodException(\sprintf('%s::%s', static::class, $method));
}

return null;
}

return static::executeStaticCallable(static::getMacro($method), ...$parameters);
}







public function setUnit(string $unit, Month|int|float|null $value = null): static
{
if (\is_float($value)) {
$int = (int) $value;

if ((float) $int !== $value) {
throw new InvalidArgumentException(
"$unit cannot be changed to float value $value, integer expected",
);
}

$value = $int;
}

$unit = static::singularUnit($unit);
$value = self::monthToInt($value, $unit);
$dateUnits = ['year', 'month', 'day'];

if (\in_array($unit, $dateUnits)) {
return $this->setDate(...array_map(
fn ($name) => (int) ($name === $unit ? $value : $this->$name),
$dateUnits,
));
}

$units = ['hour', 'minute', 'second', 'micro'];

if ($unit === 'millisecond' || $unit === 'milli') {
$value *= 1000;
$unit = 'micro';
} elseif ($unit === 'microsecond') {
$unit = 'micro';
}

return $this->setTime(...array_map(
fn ($name) => (int) ($name === $unit ? $value : $this->$name),
$units,
));
}




public static function singularUnit(string $unit): string
{
$unit = rtrim(mb_strtolower($unit), 's');

return match ($unit) {
'centurie' => 'century',
'millennia' => 'millennium',
default => $unit,
};
}




public static function pluralUnit(string $unit): string
{
$unit = rtrim(strtolower($unit), 's');

return match ($unit) {
'century' => 'centuries',
'millennium', 'millennia' => 'millennia',
default => "{$unit}s",
};
}

public static function sleep(int|float $seconds): void
{
if (static::hasTestNow()) {
static::setTestNow(static::getTestNow()->avoidMutation()->addSeconds($seconds));

return;
}

(new NativeClock('UTC'))->sleep($seconds);
}









public function __call(string $method, array $parameters): mixed
{
$unit = rtrim($method, 's');

return $this->callDiffAlias($unit, $parameters)
?? $this->callHumanDiffAlias($unit, $parameters)
?? $this->callRoundMethod($unit, $parameters)
?? $this->callIsMethod($unit, $parameters)
?? $this->callModifierMethod($unit, $parameters)
?? $this->callPeriodMethod($method, $parameters)
?? $this->callGetOrSetMethod($method, $parameters)
?? $this->callMacroMethod($method, $parameters);
}





protected function resolveCarbon(DateTimeInterface|string|null $date): self
{
if (!$date) {
return $this->nowWithSameTz();
}

if (\is_string($date)) {
return $this->transmitFactory(fn () => static::parse($date, $this->getTimezone()));
}

return $date instanceof self ? $date : $this->transmitFactory(static fn () => static::instance($date));
}

protected static function weekRotate(int $day, int $rotation): int
{
return (static::DAYS_PER_WEEK + $rotation % static::DAYS_PER_WEEK + $day) % static::DAYS_PER_WEEK;
}

protected function executeCallable(callable $macro, ...$parameters)
{
if ($macro instanceof Closure) {
$boundMacro = @$macro->bindTo($this, static::class) ?: @$macro->bindTo(null, static::class);

return \call_user_func_array($boundMacro ?: $macro, $parameters);
}

return \call_user_func_array($macro, $parameters);
}

protected function executeCallableWithContext(callable $macro, ...$parameters)
{
return static::bindMacroContext($this, function () use (&$macro, &$parameters) {
return $this->executeCallable($macro, ...$parameters);
});
}

protected function getAllGenericMacros(): Generator
{
yield from $this->localGenericMacros ?? [];
yield from $this->transmitFactory(static fn () => static::getGenericMacros());
}

protected static function getGenericMacros(): Generator
{
foreach ((FactoryImmutable::getInstance()->getSettings()['genericMacros'] ?? []) as $list) {
foreach ($list as $macro) {
yield $macro;
}
}
}

protected static function executeStaticCallable(callable $macro, ...$parameters)
{
return static::bindMacroContext(null, function () use (&$macro, &$parameters) {
if ($macro instanceof Closure) {
$boundMacro = @Closure::bind($macro, null, static::class);

return \call_user_func_array($boundMacro ?: $macro, $parameters);
}

return \call_user_func_array($macro, $parameters);
});
}

protected function getTranslatedFormByRegExp($baseKey, $keySuffix, $context, $subKey, $defaultValue)
{
$key = $baseKey.$keySuffix;
$standaloneKey = "{$key}_standalone";
$baseTranslation = $this->getTranslationMessage($key);

if ($baseTranslation instanceof Closure) {
return $baseTranslation($this, $context, $subKey) ?: $defaultValue;
}

if (
$this->getTranslationMessage("$standaloneKey.$subKey") &&
(!$context || (($regExp = $this->getTranslationMessage("{$baseKey}_regexp")) && !preg_match($regExp, $context)))
) {
$key = $standaloneKey;
}

return $this->getTranslationMessage("$key.$subKey", null, $defaultValue);
}

private function callGetOrSetMethod(string $method, array $parameters): mixed
{
if (preg_match('/^([a-z]{2,})(In|Of)([A-Z][a-z]+)$/', $method)) {
$localStrictModeEnabled = $this->localStrictModeEnabled;
$this->localStrictModeEnabled = true;

try {
return $this->callGetOrSet($method, $parameters[0] ?? null);
} catch (UnknownGetterException|UnknownSetterException|ImmutableException) {

} finally {
$this->localStrictModeEnabled = $localStrictModeEnabled;
}
}

return null;
}

private function callGetOrSet(string $name, mixed $value): mixed
{
if ($value !== null) {
if (\is_string($value) || \is_int($value) || \is_float($value) || $value instanceof DateTimeZone || $value instanceof Month) {
return $this->set($name, $value);
}

return null;
}

return $this->get($name);
}

private function getUTCUnit(string $unit): ?string
{
if (str_starts_with($unit, 'Real')) {
return substr($unit, 4);
}

if (str_starts_with($unit, 'UTC')) {
return substr($unit, 3);
}

return null;
}

private function callDiffAlias(string $method, array $parameters): mixed
{
if (preg_match('/^(diff|floatDiff)In(Real|UTC|Utc)?(.+)$/', $method, $match)) {
$mode = strtoupper($match[2] ?? '');
$betterMethod = $match[1] === 'floatDiff' ? str_replace('floatDiff', 'diff', $method) : null;

if ($mode === 'REAL') {
$mode = 'UTC';
$betterMethod = str_replace($match[2], 'UTC', $betterMethod ?? $method);
}

if ($betterMethod) {
@trigger_error(
"Use the method $betterMethod instead to make it more explicit about what it does.\n".
'On next major version, "float" prefix will be removed (as all diff are now returning floating numbers)'.
' and "Real" methods will be removed in favor of "UTC" because what it actually does is to convert both'.
' dates to UTC timezone before comparison, while by default it does it only if both dates don\'t have'.
' exactly the same timezone (Note: 2 timezones with the same offset but different names are considered'.
" different as it's not safe to assume they will always have the same offset).",
\E_USER_DEPRECATED,
);
}

$unit = self::pluralUnit($match[3]);
$diffMethod = 'diffIn'.ucfirst($unit);

if (\in_array($unit, ['days', 'weeks', 'months', 'quarters', 'years'])) {
$parameters['utc'] = ($mode === 'UTC');
}

if (method_exists($this, $diffMethod)) {
return $this->$diffMethod(...$parameters);
}
}

return null;
}

private function callHumanDiffAlias(string $method, array $parameters): ?string
{
$diffSizes = [

'short' => true,

'long' => false,
];
$diffSyntaxModes = [

'Absolute' => CarbonInterface::DIFF_ABSOLUTE,

'Relative' => CarbonInterface::DIFF_RELATIVE_AUTO,

'RelativeToNow' => CarbonInterface::DIFF_RELATIVE_TO_NOW,

'RelativeToOther' => CarbonInterface::DIFF_RELATIVE_TO_OTHER,
];
$sizePattern = implode('|', array_keys($diffSizes));
$syntaxPattern = implode('|', array_keys($diffSyntaxModes));

if (preg_match("/^(?<size>$sizePattern)(?<syntax>$syntaxPattern)DiffForHuman$/", $method, $match)) {
$dates = array_filter($parameters, function ($parameter) {
return $parameter instanceof DateTimeInterface;
});
$other = null;

if (\count($dates)) {
$key = key($dates);
$other = current($dates);
array_splice($parameters, $key, 1);
}

return $this->diffForHumans($other, $diffSyntaxModes[$match['syntax']], $diffSizes[$match['size']], ...$parameters);
}

return null;
}

private function callIsMethod(string $unit, array $parameters): ?bool
{
if (!str_starts_with($unit, 'is')) {
return null;
}

$word = substr($unit, 2);

if (\in_array($word, static::$days, true)) {
return $this->isDayOfWeek($word);
}

return match ($word) {

'Utc', 'UTC' => $this->utc,

'Local' => $this->local,

'Valid' => $this->year !== 0,

'DST' => $this->dst,
default => $this->callComparatorMethod($word, $parameters),
};
}

private function callComparatorMethod(string $unit, array $parameters): ?bool
{
$start = substr($unit, 0, 4);
$factor = -1;

if ($start === 'Last') {
$start = 'Next';
$factor = 1;
}

if ($start === 'Next') {
$lowerUnit = strtolower(substr($unit, 4));

if (static::isModifiableUnit($lowerUnit)) {
return $this->avoidMutation()->addUnit($lowerUnit, $factor, false)->isSameUnit($lowerUnit, ...($parameters ?: ['now']));
}
}

if ($start === 'Same') {
try {
return $this->isSameUnit(strtolower(substr($unit, 4)), ...$parameters);
} catch (BadComparisonUnitException) {

}
}

if (str_starts_with($unit, 'Current')) {
try {
return $this->isCurrentUnit(strtolower(substr($unit, 7)));
} catch (BadComparisonUnitException | BadMethodCallException) {

}
}

return null;
}

private function callModifierMethod(string $unit, array $parameters): ?static
{
$action = substr($unit, 0, 3);
$overflow = null;

if ($action === 'set') {
$unit = strtolower(substr($unit, 3));
}

if (\in_array($unit, static::$units, true)) {
return $this->setUnit($unit, ...$parameters);
}

if ($action === 'add' || $action === 'sub') {
$unit = substr($unit, 3);
$utcUnit = $this->getUTCUnit($unit);

if ($utcUnit) {
$unit = static::singularUnit($utcUnit);

return $this->{"{$action}UTCUnit"}($unit, ...$parameters);
}

if (preg_match('/^(Month|Quarter|Year|Decade|Century|Centurie|Millennium|Millennia)s?(No|With|Without|WithNo)Overflow$/', $unit, $match)) {
$unit = $match[1];
$overflow = $match[2] === 'With';
}

$unit = static::singularUnit($unit);
}

if (static::isModifiableUnit($unit)) {
return $this->{"{$action}Unit"}($unit, $this->getMagicParameter($parameters, 0, 'value', 1), $overflow);
}

return null;
}

private function callPeriodMethod(string $method, array $parameters): ?CarbonPeriod
{
if (str_ends_with($method, 'Until')) {
try {
$unit = static::singularUnit(substr($method, 0, -5));

return $this->range(
$this->getMagicParameter($parameters, 0, 'endDate', $this),
$this->getMagicParameter($parameters, 1, 'factor', 1),
$unit
);
} catch (InvalidArgumentException) {

}
}

return null;
}

private function callMacroMethod(string $method, array $parameters): mixed
{
return static::bindMacroContext($this, function () use (&$method, &$parameters) {
$macro = $this->getLocalMacro($method);

if (!$macro) {
foreach ($this->getAllGenericMacros() as $callback) {
try {
return $this->executeCallable($callback, $method, ...$parameters);
} catch (BadMethodCallException) {
continue;
}
}

if ($this->isLocalStrictModeEnabled()) {
throw new UnknownMethodException($method);
}

return null;
}

return $this->executeCallable($macro, ...$parameters);
});
}

private static function floorZeroPad(int|float $value, int $length): string
{
return str_pad((string) floor($value), $length, '0', STR_PAD_LEFT);
}

/**
@template




*/
private function mutateIfMutable(CarbonInterface $date): CarbonInterface
{
return $this instanceof DateTimeImmutable
? $date
: $this->modify('@'.$date->rawFormat('U.u'))->setTimezone($date->getTimezone());
}
}
