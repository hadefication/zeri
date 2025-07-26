<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Exceptions\BadFluentConstructorException;
use Carbon\Exceptions\BadFluentSetterException;
use Carbon\Exceptions\InvalidCastException;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidIntervalException;
use Carbon\Exceptions\OutOfRangeException;
use Carbon\Exceptions\ParseErrorException;
use Carbon\Exceptions\UnitNotConfiguredException;
use Carbon\Exceptions\UnknownGetterException;
use Carbon\Exceptions\UnknownSetterException;
use Carbon\Exceptions\UnknownUnitException;
use Carbon\Traits\IntervalRounding;
use Carbon\Traits\IntervalStep;
use Carbon\Traits\LocalFactory;
use Carbon\Traits\MagicParameter;
use Carbon\Traits\Mixin;
use Carbon\Traits\Options;
use Carbon\Traits\ToStringFormat;
use Closure;
use DateInterval;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use ReflectionException;
use ReturnTypeWillChange;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
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






































































































































*/
class CarbonInterval extends DateInterval implements CarbonConverterInterface
{
use LocalFactory;
use IntervalRounding;
use IntervalStep;
use MagicParameter;
use Mixin {
Mixin::mixin as baseMixin;
}
use Options;
use ToStringFormat;






public const NO_LIMIT = -1;

public const POSITIVE = 1;
public const NEGATIVE = -1;




public const PERIOD_PREFIX = 'P';
public const PERIOD_YEARS = 'Y';
public const PERIOD_MONTHS = 'M';
public const PERIOD_DAYS = 'D';
public const PERIOD_TIME_PREFIX = 'T';
public const PERIOD_HOURS = 'H';
public const PERIOD_MINUTES = 'M';
public const PERIOD_SECONDS = 'S';

public const SPECIAL_TRANSLATIONS = [
1 => [
'option' => CarbonInterface::ONE_DAY_WORDS,
'future' => 'diff_tomorrow',
'past' => 'diff_yesterday',
],
2 => [
'option' => CarbonInterface::TWO_DAY_WORDS,
'future' => 'diff_after_tomorrow',
'past' => 'diff_before_yesterday',
],
];

protected static ?array $cascadeFactors = null;

protected static array $formats = [
'y' => 'y',
'Y' => 'y',
'o' => 'y',
'm' => 'm',
'n' => 'm',
'W' => 'weeks',
'd' => 'd',
'j' => 'd',
'z' => 'd',
'h' => 'h',
'g' => 'h',
'H' => 'h',
'G' => 'h',
'i' => 'i',
's' => 's',
'u' => 'micro',
'v' => 'milli',
];

private static ?array $flipCascadeFactors = null;

private static bool $floatSettersEnabled = false;




protected static array $macros = [];




protected DateTimeZone|string|int|null $timezoneSetting = null;




protected mixed $originalInput = null;




protected ?CarbonInterface $startDate = null;




protected ?CarbonInterface $endDate = null;




protected ?DateInterval $rawInterval = null;




protected bool $absolute = false;

protected ?array $initialValues = null;




public function setTimezone(DateTimeZone|string|int $timezone): static
{
$this->timezoneSetting = $timezone;
$this->checkStartAndEnd();

if ($this->startDate) {
$this->startDate = $this->startDate
->avoidMutation()
->setTimezone($timezone);
$this->rawInterval = null;
}

if ($this->endDate) {
$this->endDate = $this->endDate
->avoidMutation()
->setTimezone($timezone);
$this->rawInterval = null;
}

return $this;
}




public function shiftTimezone(DateTimeZone|string|int $timezone): static
{
$this->timezoneSetting = $timezone;
$this->checkStartAndEnd();

if ($this->startDate) {
$this->startDate = $this->startDate
->avoidMutation()
->shiftTimezone($timezone);
$this->rawInterval = null;
}

if ($this->endDate) {
$this->endDate = $this->endDate
->avoidMutation()
->shiftTimezone($timezone);
$this->rawInterval = null;
}

return $this;
}






public static function getCascadeFactors(): array
{
return static::$cascadeFactors ?: static::getDefaultCascadeFactors();
}

protected static function getDefaultCascadeFactors(): array
{
return [
'milliseconds' => [CarbonInterface::MICROSECONDS_PER_MILLISECOND, 'microseconds'],
'seconds' => [CarbonInterface::MILLISECONDS_PER_SECOND, 'milliseconds'],
'minutes' => [CarbonInterface::SECONDS_PER_MINUTE, 'seconds'],
'hours' => [CarbonInterface::MINUTES_PER_HOUR, 'minutes'],
'dayz' => [CarbonInterface::HOURS_PER_DAY, 'hours'],
'weeks' => [CarbonInterface::DAYS_PER_WEEK, 'dayz'],
'months' => [CarbonInterface::WEEKS_PER_MONTH, 'weeks'],
'years' => [CarbonInterface::MONTHS_PER_YEAR, 'months'],
];
}






public static function setCascadeFactors(array $cascadeFactors)
{
self::$flipCascadeFactors = null;
static::$cascadeFactors = $cascadeFactors;
}









public static function enableFloatSetters(bool $floatSettersEnabled = true): void
{
self::$floatSettersEnabled = $floatSettersEnabled;
}



















public function __construct($years = null, $months = null, $weeks = null, $days = null, $hours = null, $minutes = null, $seconds = null, $microseconds = null)
{
$this->originalInput = \func_num_args() === 1 ? $years : \func_get_args();

if ($years instanceof Closure) {
$this->step = $years;
$years = null;
}

if ($years instanceof DateInterval) {
parent::__construct(static::getDateIntervalSpec($years));
$this->f = $years->f;
self::copyNegativeUnits($years, $this);

return;
}

$spec = $years;
$isStringSpec = (\is_string($spec) && !preg_match('/^[\d.]/', $spec));

if (!$isStringSpec || (float) $years) {
$spec = static::PERIOD_PREFIX;

$spec .= $years > 0 ? $years.static::PERIOD_YEARS : '';
$spec .= $months > 0 ? $months.static::PERIOD_MONTHS : '';

$specDays = 0;
$specDays += $weeks > 0 ? $weeks * static::getDaysPerWeek() : 0;
$specDays += $days > 0 ? $days : 0;

$spec .= $specDays > 0 ? $specDays.static::PERIOD_DAYS : '';

if ($hours > 0 || $minutes > 0 || $seconds > 0) {
$spec .= static::PERIOD_TIME_PREFIX;
$spec .= $hours > 0 ? $hours.static::PERIOD_HOURS : '';
$spec .= $minutes > 0 ? $minutes.static::PERIOD_MINUTES : '';
$spec .= $seconds > 0 ? $seconds.static::PERIOD_SECONDS : '';
}

if ($spec === static::PERIOD_PREFIX) {

$spec .= '0'.static::PERIOD_YEARS;
}
}

try {
parent::__construct($spec);
} catch (Throwable $exception) {
try {
parent::__construct('PT0S');

if ($isStringSpec) {
if (!preg_match('/^P
                        (?:(?<year>[+-]?\d*(?:\.\d+)?)Y)?
                        (?:(?<month>[+-]?\d*(?:\.\d+)?)M)?
                        (?:(?<week>[+-]?\d*(?:\.\d+)?)W)?
                        (?:(?<day>[+-]?\d*(?:\.\d+)?)D)?
                        (?:T
                            (?:(?<hour>[+-]?\d*(?:\.\d+)?)H)?
                            (?:(?<minute>[+-]?\d*(?:\.\d+)?)M)?
                            (?:(?<second>[+-]?\d*(?:\.\d+)?)S)?
                        )?
                    $/x', $spec, $match)) {
throw new InvalidArgumentException("Invalid duration: $spec");
}

$years = (float) ($match['year'] ?? 0);
$this->assertSafeForInteger('year', $years);
$months = (float) ($match['month'] ?? 0);
$this->assertSafeForInteger('month', $months);
$weeks = (float) ($match['week'] ?? 0);
$this->assertSafeForInteger('week', $weeks);
$days = (float) ($match['day'] ?? 0);
$this->assertSafeForInteger('day', $days);
$hours = (float) ($match['hour'] ?? 0);
$this->assertSafeForInteger('hour', $hours);
$minutes = (float) ($match['minute'] ?? 0);
$this->assertSafeForInteger('minute', $minutes);
$seconds = (float) ($match['second'] ?? 0);
$this->assertSafeForInteger('second', $seconds);
$microseconds = (int) str_pad(
substr(explode('.', $match['second'] ?? '0.0')[1] ?? '0', 0, 6),
6,
'0',
);
}

$totalDays = (($weeks * static::getDaysPerWeek()) + $days);
$this->assertSafeForInteger('days total (including weeks)', $totalDays);

$this->y = (int) $years;
$this->m = (int) $months;
$this->d = (int) $totalDays;
$this->h = (int) $hours;
$this->i = (int) $minutes;
$this->s = (int) $seconds;
$secondFloatPart = (float) ($microseconds / CarbonInterface::MICROSECONDS_PER_SECOND);
$this->f = $secondFloatPart;
$intervalMicroseconds = (int) ($this->f * CarbonInterface::MICROSECONDS_PER_SECOND);
$intervalSeconds = $seconds - $secondFloatPart;

if (
((float) $this->y) !== $years ||
((float) $this->m) !== $months ||
((float) $this->d) !== $totalDays ||
((float) $this->h) !== $hours ||
((float) $this->i) !== $minutes ||
((float) $this->s) !== $intervalSeconds ||
$intervalMicroseconds !== ((int) $microseconds)
) {
$this->add(static::fromString(
($years - $this->y).' years '.
($months - $this->m).' months '.
($totalDays - $this->d).' days '.
($hours - $this->h).' hours '.
($minutes - $this->i).' minutes '.
number_format($intervalSeconds - $this->s, 6, '.', '').' seconds '.
($microseconds - $intervalMicroseconds).' microseconds ',
));
}
} catch (Throwable $secondException) {
throw $secondException instanceof OutOfRangeException ? $secondException : $exception;
}
}

if ($microseconds !== null) {
$this->f = $microseconds / CarbonInterface::MICROSECONDS_PER_SECOND;
}

foreach (['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds'] as $unit) {
if ($$unit < 0) {
$this->set($unit, $$unit);
}
}
}









public static function getFactor($source, $target)
{
$source = self::standardizeUnit($source);
$target = self::standardizeUnit($target);
$factors = self::getFlipCascadeFactors();

if (isset($factors[$source])) {
[$to, $factor] = $factors[$source];

if ($to === $target) {
return $factor;
}

return $factor * static::getFactor($to, $target);
}

return null;
}










public static function getFactorWithDefault($source, $target)
{
$factor = self::getFactor($source, $target);

if ($factor) {
return $factor;
}

static $defaults = [
'month' => ['year' => Carbon::MONTHS_PER_YEAR],
'week' => ['month' => Carbon::WEEKS_PER_MONTH],
'day' => ['week' => Carbon::DAYS_PER_WEEK],
'hour' => ['day' => Carbon::HOURS_PER_DAY],
'minute' => ['hour' => Carbon::MINUTES_PER_HOUR],
'second' => ['minute' => Carbon::SECONDS_PER_MINUTE],
'millisecond' => ['second' => Carbon::MILLISECONDS_PER_SECOND],
'microsecond' => ['millisecond' => Carbon::MICROSECONDS_PER_MILLISECOND],
];

return $defaults[$source][$target] ?? null;
}






public static function getDaysPerWeek()
{
return static::getFactor('dayz', 'weeks') ?: Carbon::DAYS_PER_WEEK;
}






public static function getHoursPerDay()
{
return static::getFactor('hours', 'dayz') ?: Carbon::HOURS_PER_DAY;
}






public static function getMinutesPerHour()
{
return static::getFactor('minutes', 'hours') ?: Carbon::MINUTES_PER_HOUR;
}






public static function getSecondsPerMinute()
{
return static::getFactor('seconds', 'minutes') ?: Carbon::SECONDS_PER_MINUTE;
}






public static function getMillisecondsPerSecond()
{
return static::getFactor('milliseconds', 'seconds') ?: Carbon::MILLISECONDS_PER_SECOND;
}






public static function getMicrosecondsPerMillisecond()
{
return static::getFactor('microseconds', 'milliseconds') ?: Carbon::MICROSECONDS_PER_MILLISECOND;
}




















public static function create($years = null, $months = null, $weeks = null, $days = null, $hours = null, $minutes = null, $seconds = null, $microseconds = null)
{
return new static($years, $months, $weeks, $days, $hours, $minutes, $seconds, $microseconds);
}
















public static function createFromFormat(string $format, ?string $interval): static
{
$instance = new static(0);
$length = mb_strlen($format);

if (preg_match('/s([,.])([uv])$/', $format, $match)) {
$interval = explode($match[1], $interval);
$index = \count($interval) - 1;
$interval[$index] = str_pad($interval[$index], $match[2] === 'v' ? 3 : 6, '0');
$interval = implode($match[1], $interval);
}

$interval ??= '';

for ($index = 0; $index < $length; $index++) {
$expected = mb_substr($format, $index, 1);
$nextCharacter = mb_substr($interval, 0, 1);
$unit = static::$formats[$expected] ?? null;

if ($unit) {
if (!preg_match('/^-?\d+/', $interval, $match)) {
throw new ParseErrorException('number', $nextCharacter);
}

$interval = mb_substr($interval, mb_strlen($match[0]));
self::incrementUnit($instance, $unit, (int) ($match[0]));

continue;
}

if ($nextCharacter !== $expected) {
throw new ParseErrorException(
"'$expected'",
$nextCharacter,
'Allowed substitutes for interval formats are '.implode(', ', array_keys(static::$formats))."\n".
'See https://php.net/manual/en/function.date.php for their meaning',
);
}

$interval = mb_substr($interval, 1);
}

if ($interval !== '') {
throw new ParseErrorException(
'end of string',
$interval,
);
}

return $instance;
}






public function original()
{
return $this->originalInput;
}






public function start(): ?CarbonInterface
{
$this->checkStartAndEnd();

return $this->startDate;
}






public function end(): ?CarbonInterface
{
$this->checkStartAndEnd();

return $this->endDate;
}






public function optimize(): static
{
$this->originalInput = null;
$this->startDate = null;
$this->endDate = null;
$this->rawInterval = null;
$this->absolute = false;

return $this;
}






public function copy(): static
{
$date = new static(0);
$date->copyProperties($this);
$date->step = $this->step;

return $date;
}






public function clone(): static
{
return $this->copy();
}












public static function __callStatic(string $method, array $parameters)
{
try {
$interval = new static(0);
$localStrictModeEnabled = $interval->localStrictModeEnabled;
$interval->localStrictModeEnabled = true;

$result = static::hasMacro($method)
? static::bindMacroContext(null, function () use (&$method, &$parameters, &$interval) {
return $interval->callMacro($method, $parameters);
})
: $interval->$method(...$parameters);

$interval->localStrictModeEnabled = $localStrictModeEnabled;

return $result;
} catch (BadFluentSetterException $exception) {
if (Carbon::isStrictModeEnabled()) {
throw new BadFluentConstructorException($method, 0, $exception);
}

return null;
}
}








#[ReturnTypeWillChange]
public static function __set_state($dump)
{
/**
@noinspection */

$dateInterval = parent::__set_state($dump);

return static::instance($dateInterval);
}






protected static function this(): static
{
return end(static::$macroContextStack) ?: new static(0);
}





























public static function fromString(string $intervalDefinition): static
{
if (empty($intervalDefinition)) {
return self::withOriginal(new static(0), $intervalDefinition);
}

$years = 0;
$months = 0;
$weeks = 0;
$days = 0;
$hours = 0;
$minutes = 0;
$seconds = 0;
$milliseconds = 0;
$microseconds = 0;

$pattern = '/(-?\d+(?:\.\d+)?)\h*([^\d\h]*)/i';
preg_match_all($pattern, $intervalDefinition, $parts, PREG_SET_ORDER);

while ([$part, $value, $unit] = array_shift($parts)) {
$intValue = (int) $value;
$fraction = (float) $value - $intValue;


switch (round($fraction, 6)) {
case 1:
$fraction = 0;
$intValue++;

break;
case 0:
$fraction = 0;

break;
}

switch ($unit === 'µs' ? 'µs' : strtolower($unit)) {
case 'millennia':
case 'millennium':
$years += $intValue * CarbonInterface::YEARS_PER_MILLENNIUM;

break;

case 'century':
case 'centuries':
$years += $intValue * CarbonInterface::YEARS_PER_CENTURY;

break;

case 'decade':
case 'decades':
$years += $intValue * CarbonInterface::YEARS_PER_DECADE;

break;

case 'year':
case 'years':
case 'y':
case 'yr':
case 'yrs':
$years += $intValue;

break;

case 'quarter':
case 'quarters':
$months += $intValue * CarbonInterface::MONTHS_PER_QUARTER;

break;

case 'month':
case 'months':
case 'mo':
case 'mos':
$months += $intValue;

break;

case 'week':
case 'weeks':
case 'w':
$weeks += $intValue;

if ($fraction) {
$parts[] = [null, $fraction * static::getDaysPerWeek(), 'd'];
}

break;

case 'day':
case 'days':
case 'd':
$days += $intValue;

if ($fraction) {
$parts[] = [null, $fraction * static::getHoursPerDay(), 'h'];
}

break;

case 'hour':
case 'hours':
case 'h':
$hours += $intValue;

if ($fraction) {
$parts[] = [null, $fraction * static::getMinutesPerHour(), 'm'];
}

break;

case 'minute':
case 'minutes':
case 'm':
$minutes += $intValue;

if ($fraction) {
$parts[] = [null, $fraction * static::getSecondsPerMinute(), 's'];
}

break;

case 'second':
case 'seconds':
case 's':
$seconds += $intValue;

if ($fraction) {
$parts[] = [null, $fraction * static::getMillisecondsPerSecond(), 'ms'];
}

break;

case 'millisecond':
case 'milliseconds':
case 'milli':
case 'ms':
$milliseconds += $intValue;

if ($fraction) {
$microseconds += round($fraction * static::getMicrosecondsPerMillisecond());
}

break;

case 'microsecond':
case 'microseconds':
case 'micro':
case 'µs':
$microseconds += $intValue;

break;

default:
throw new InvalidIntervalException(
\sprintf('Invalid part %s in definition %s', $part, $intervalDefinition),
);
}
}

return self::withOriginal(
new static($years, $months, $weeks, $days, $hours, $minutes, $seconds, $milliseconds * Carbon::MICROSECONDS_PER_MILLISECOND + $microseconds),
$intervalDefinition,
);
}









public static function parseFromLocale(string $interval, ?string $locale = null): static
{
return static::fromString(Carbon::translateTimeString($interval, $locale ?: static::getLocale(), CarbonInterface::DEFAULT_LOCALE));
}









public static function diff($start, $end = null, bool $absolute = false, array $skip = []): static
{
$start = $start instanceof CarbonInterface ? $start : Carbon::make($start);
$end = $end instanceof CarbonInterface ? $end : Carbon::make($end);
$rawInterval = $start->diffAsDateInterval($end, $absolute);
$interval = static::instance($rawInterval, $skip);

$interval->absolute = $absolute;
$interval->rawInterval = $rawInterval;
$interval->startDate = $start;
$interval->endDate = $end;
$interval->initialValues = $interval->getInnerValues();

return $interval;
}








public function abs(bool $absolute = false): static
{
if ($absolute && $this->invert) {
$this->invert();
}

return $this;
}

/**
@alias






*/
public function absolute(bool $absolute = true): static
{
return $this->abs($absolute);
}

/**
@template
@psalm-param





*/
public function cast(string $className): mixed
{
return self::castIntervalToClass($this, $className);
}













public static function instance(DateInterval $interval, array $skip = [], bool $skipCopy = false): static
{
if ($skipCopy && $interval instanceof static) {
return $interval;
}

return self::castIntervalToClass($interval, static::class, $skip);
}















public static function make($interval, $unit = null, bool $skipCopy = false): ?self
{
if ($interval instanceof Unit) {
$interval = $interval->interval();
}

if ($unit instanceof Unit) {
$unit = $unit->value;
}

if ($unit) {
$interval = "$interval $unit";
}

if ($interval instanceof DateInterval) {
return static::instance($interval, [], $skipCopy);
}

if ($interval instanceof Closure) {
return self::withOriginal(new static($interval), $interval);
}

if (!\is_string($interval)) {
return null;
}

return static::makeFromString($interval);
}

protected static function makeFromString(string $interval): ?self
{
$interval = preg_replace('/\s+/', ' ', trim($interval));

if (preg_match('/^P[T\d]/', $interval)) {
return new static($interval);
}

if (preg_match('/^(?:\h*-?\d+(?:\.\d+)?\h*[a-z]+)+$/i', $interval)) {
return static::fromString($interval);
}

$intervalInstance = static::createFromDateString($interval);

return $intervalInstance->isEmpty() ? null : $intervalInstance;
}

protected function resolveInterval($interval): ?self
{
if (!($interval instanceof self)) {
return self::make($interval);
}

return $interval;
}










public static function createFromDateString(string $datetime): static
{
$string = strtr($datetime, [
',' => ' ',
' and ' => ' ',
]);
$previousException = null;

try {
$interval = parent::createFromDateString($string);
} catch (Throwable $exception) {
$interval = null;
$previousException = $exception;
}

$interval ?: throw new InvalidFormatException(
'Could not create interval from: '.var_export($datetime, true),
previous: $previousException,
);

if (!($interval instanceof static)) {
$interval = static::instance($interval);
}

return self::withOriginal($interval, $datetime);
}








public function get(Unit|string $name): int|float|string|null
{
$name = Unit::toName($name);

if (str_starts_with($name, 'total')) {
return $this->total(substr($name, 5));
}

$resolvedUnit = Carbon::singularUnit(rtrim($name, 'z'));

return match ($resolvedUnit) {
'tzname', 'tz_name' => match (true) {
($this->timezoneSetting === null) => null,
\is_string($this->timezoneSetting) => $this->timezoneSetting,
($this->timezoneSetting instanceof DateTimeZone) => $this->timezoneSetting->getName(),
default => CarbonTimeZone::instance($this->timezoneSetting)->getName(),
},
'year' => $this->y,
'month' => $this->m,
'day' => $this->d,
'hour' => $this->h,
'minute' => $this->i,
'second' => $this->s,
'milli', 'millisecond' => (int) (round($this->f * Carbon::MICROSECONDS_PER_SECOND) /
Carbon::MICROSECONDS_PER_MILLISECOND),
'micro', 'microsecond' => (int) round($this->f * Carbon::MICROSECONDS_PER_SECOND),
'microexcludemilli' => (int) round($this->f * Carbon::MICROSECONDS_PER_SECOND) %
Carbon::MICROSECONDS_PER_MILLISECOND,
'week' => (int) ($this->d / (int) static::getDaysPerWeek()),
'daysexcludeweek', 'dayzexcludeweek' => $this->d % (int) static::getDaysPerWeek(),
'locale' => $this->getTranslatorLocale(),
default => throw new UnknownGetterException($name, previous: new UnknownGetterException($resolvedUnit)),
};
}




public function __get(string $name): int|float|string|null
{
return $this->get($name);
}











public function set($name, $value = null): static
{
$properties = \is_array($name) ? $name : [$name => $value];

foreach ($properties as $key => $value) {
switch (Carbon::singularUnit($key instanceof Unit ? $key->value : rtrim((string) $key, 'z'))) {
case 'year':
$this->checkIntegerValue($key, $value);
$this->y = $value;
$this->handleDecimalPart('year', $value, $this->y);

break;

case 'month':
$this->checkIntegerValue($key, $value);
$this->m = $value;
$this->handleDecimalPart('month', $value, $this->m);

break;

case 'week':
$this->checkIntegerValue($key, $value);
$days = $value * (int) static::getDaysPerWeek();
$this->assertSafeForInteger('days total (including weeks)', $days);
$this->d = $days;
$this->handleDecimalPart('day', $days, $this->d);

break;

case 'day':
if ($value === false) {
break;
}

$this->checkIntegerValue($key, $value);
$this->d = $value;
$this->handleDecimalPart('day', $value, $this->d);

break;

case 'daysexcludeweek':
case 'dayzexcludeweek':
$this->checkIntegerValue($key, $value);
$days = $this->weeks * (int) static::getDaysPerWeek() + $value;
$this->assertSafeForInteger('days total (including weeks)', $days);
$this->d = $days;
$this->handleDecimalPart('day', $days, $this->d);

break;

case 'hour':
$this->checkIntegerValue($key, $value);
$this->h = $value;
$this->handleDecimalPart('hour', $value, $this->h);

break;

case 'minute':
$this->checkIntegerValue($key, $value);
$this->i = $value;
$this->handleDecimalPart('minute', $value, $this->i);

break;

case 'second':
$this->checkIntegerValue($key, $value);
$this->s = $value;
$this->handleDecimalPart('second', $value, $this->s);

break;

case 'milli':
case 'millisecond':
$this->microseconds = $value * Carbon::MICROSECONDS_PER_MILLISECOND + $this->microseconds % Carbon::MICROSECONDS_PER_MILLISECOND;

break;

case 'micro':
case 'microsecond':
$this->f = $value / Carbon::MICROSECONDS_PER_SECOND;

break;

default:
if (str_starts_with($key, ' * ')) {
return $this->setSetting(substr($key, 3), $value);
}

if ($this->localStrictModeEnabled ?? Carbon::isStrictModeEnabled()) {
throw new UnknownSetterException($key);
}

$this->$key = $value;
}
}

return $this;
}









public function __set(string $name, $value)
{
$this->set($name, $value);
}









public function weeksAndDays(int $weeks, int $days): static
{
$this->dayz = ($weeks * static::getDaysPerWeek()) + $days;

return $this;
}






public function isEmpty(): bool
{
return $this->years === 0 &&
$this->months === 0 &&
$this->dayz === 0 &&
!$this->days &&
$this->hours === 0 &&
$this->minutes === 0 &&
$this->seconds === 0 &&
$this->microseconds === 0;
}

/**
@param-closure-this












*/
public static function macro(string $name, ?callable $macro): void
{
static::$macros[$name] = $macro;
}


































public static function mixin($mixin): void
{
static::baseMixin($mixin);
}








public static function hasMacro(string $name): bool
{
return isset(static::$macros[$name]);
}









protected function callMacro(string $name, array $parameters)
{
$macro = static::$macros[$name];

if ($macro instanceof Closure) {
$boundMacro = @$macro->bindTo($this, static::class) ?: @$macro->bindTo(null, static::class);

return ($boundMacro ?: $macro)(...$parameters);
}

return $macro(...$parameters);
}














public function __call(string $method, array $parameters)
{
if (static::hasMacro($method)) {
return static::bindMacroContext($this, function () use (&$method, &$parameters) {
return $this->callMacro($method, $parameters);
});
}

$roundedValue = $this->callRoundMethod($method, $parameters);

if ($roundedValue !== null) {
return $roundedValue;
}

if (preg_match('/^(?<method>add|sub)(?<unit>[A-Z].*)$/', $method, $match)) {
$value = $this->getMagicParameter($parameters, 0, Carbon::pluralUnit($match['unit']), 0);

return $this->{$match['method']}($value, $match['unit']);
}

$value = $this->getMagicParameter($parameters, 0, Carbon::pluralUnit($method), 1);

try {
$this->set($method, $value);
} catch (UnknownSetterException $exception) {
if ($this->localStrictModeEnabled ?? Carbon::isStrictModeEnabled()) {
throw new BadFluentSetterException($method, 0, $exception);
}
}

return $this;
}

protected function getForHumansInitialVariables($syntax, $short): array
{
if (\is_array($syntax)) {
return $syntax;
}

if (\is_int($short)) {
return [
'parts' => $short,
'short' => false,
];
}

if (\is_bool($syntax)) {
return [
'short' => $syntax,
'syntax' => CarbonInterface::DIFF_ABSOLUTE,
];
}

return [];
}









protected function getForHumansParameters($syntax = null, $short = false, $parts = self::NO_LIMIT, $options = null): array
{
$optionalSpace = ' ';
$default = $this->getTranslationMessage('list.0') ?? $this->getTranslationMessage('list') ?? ' ';

$join = $default === '' ? '' : ' ';

$altNumbers = false;
$aUnit = false;
$minimumUnit = 's';
$skip = [];
extract($this->getForHumansInitialVariables($syntax, $short));
$skip = array_map(
static fn ($unit) => $unit instanceof Unit ? $unit->value : $unit,
(array) $skip,
);
$skip = array_map(
'strtolower',
array_filter($skip, static fn ($unit) => \is_string($unit) && $unit !== ''),
);

$syntax ??= CarbonInterface::DIFF_ABSOLUTE;

if ($parts === self::NO_LIMIT) {
$parts = INF;
}

$options ??= static::getHumanDiffOptions();

if ($join === false) {
$join = ' ';
} elseif ($join === true) {
$join = [
$default,
$this->getTranslationMessage('list.1') ?? $default,
];
}

if ($altNumbers && $altNumbers !== true) {
$language = new Language($this->locale);
$altNumbers = \in_array($language->getCode(), (array) $altNumbers, true);
}

if (\is_array($join)) {
[$default, $last] = $join;

if ($default !== ' ') {
$optionalSpace = '';
}

$join = function ($list) use ($default, $last) {
if (\count($list) < 2) {
return implode('', $list);
}

$end = array_pop($list);

return implode($default, $list).$last.$end;
};
}

if (\is_string($join)) {
if ($join !== ' ') {
$optionalSpace = '';
}

$glue = $join;
$join = static fn ($list) => implode($glue, $list);
}

$interpolations = [
':optional-space' => $optionalSpace,
];

$translator ??= isset($locale) ? Translator::get($locale) : null;

return [$syntax, $short, $parts, $options, $join, $aUnit, $altNumbers, $interpolations, $minimumUnit, $skip, $translator];
}

protected static function getRoundingMethodFromOptions(int $options): ?string
{
if ($options & CarbonInterface::ROUND) {
return 'round';
}

if ($options & CarbonInterface::CEIL) {
return 'ceil';
}

if ($options & CarbonInterface::FLOOR) {
return 'floor';
}

return null;
}






public function toArray(): array
{
return [
'years' => $this->years,
'months' => $this->months,
'weeks' => $this->weeks,
'days' => $this->daysExcludeWeeks,
'hours' => $this->hours,
'minutes' => $this->minutes,
'seconds' => $this->seconds,
'microseconds' => $this->microseconds,
];
}






public function getNonZeroValues(): array
{
return array_filter($this->toArray(), 'intval');
}







public function getValuesSequence(): array
{
$nonZeroValues = $this->getNonZeroValues();

if ($nonZeroValues === []) {
return [];
}

$keys = array_keys($nonZeroValues);
$firstKey = $keys[0];
$lastKey = $keys[\count($keys) - 1];
$values = [];
$record = false;

foreach ($this->toArray() as $unit => $count) {
if ($unit === $firstKey) {
$record = true;
}

if ($record) {
$values[$unit] = $count;
}

if ($unit === $lastKey) {
$record = false;
}
}

return $values;
}



















































public function forHumans($syntax = null, $short = false, $parts = self::NO_LIMIT, $options = null): string
{

[$syntax, $short, $parts, $options, $join, $aUnit, $altNumbers, $interpolations, $minimumUnit, $skip, $translator] = $this
->getForHumansParameters($syntax, $short, $parts, $options);

$interval = [];

$syntax = (int) ($syntax ?? CarbonInterface::DIFF_ABSOLUTE);
$absolute = $syntax === CarbonInterface::DIFF_ABSOLUTE;
$relativeToNow = $syntax === CarbonInterface::DIFF_RELATIVE_TO_NOW;
$count = 1;
$unit = $short ? 's' : 'second';
$isFuture = $this->invert === 1;
$transId = $relativeToNow ? ($isFuture ? 'from_now' : 'ago') : ($isFuture ? 'after' : 'before');
$declensionMode = null;

$translator ??= $this->getLocalTranslator();

$handleDeclensions = function ($unit, $count, $index = 0, $parts = 1) use ($interpolations, $transId, $translator, $altNumbers, $absolute, &$declensionMode) {
if (!$absolute) {
$declensionMode = $declensionMode ?? $this->translate($transId.'_mode');

if ($this->needsDeclension($declensionMode, $index, $parts)) {

$key = $unit.'_'.$transId;
$result = $this->translate($key, $interpolations, $count, $translator, $altNumbers);

if ($result !== $key) {
return $result;
}
}
}

$result = $this->translate($unit, $interpolations, $count, $translator, $altNumbers);

if ($result !== $unit) {
return $result;
}

return null;
};

$intervalValues = $this;
$method = static::getRoundingMethodFromOptions($options);

if ($method) {
$previousCount = INF;

while (
\count($intervalValues->getNonZeroValues()) > $parts &&
($count = \count($keys = array_keys($intervalValues->getValuesSequence()))) > 1
) {
$index = min($count, $previousCount - 1) - 2;

if ($index < 0) {
break;
}

$intervalValues = $this->copy()->roundUnit(
$keys[$index],
1,
$method,
);
$previousCount = $count;
}
}

$diffIntervalArray = [
['value' => $intervalValues->years, 'unit' => 'year', 'unitShort' => 'y'],
['value' => $intervalValues->months, 'unit' => 'month', 'unitShort' => 'm'],
['value' => $intervalValues->weeks, 'unit' => 'week', 'unitShort' => 'w'],
['value' => $intervalValues->daysExcludeWeeks, 'unit' => 'day', 'unitShort' => 'd'],
['value' => $intervalValues->hours, 'unit' => 'hour', 'unitShort' => 'h'],
['value' => $intervalValues->minutes, 'unit' => 'minute', 'unitShort' => 'min'],
['value' => $intervalValues->seconds, 'unit' => 'second', 'unitShort' => 's'],
['value' => $intervalValues->milliseconds, 'unit' => 'millisecond', 'unitShort' => 'ms'],
['value' => $intervalValues->microExcludeMilli, 'unit' => 'microsecond', 'unitShort' => 'µs'],
];

if (!empty($skip)) {
foreach ($diffIntervalArray as $index => &$unitData) {
$nextIndex = $index + 1;

if ($unitData['value'] &&
isset($diffIntervalArray[$nextIndex]) &&
\count(array_intersect([$unitData['unit'], $unitData['unit'].'s', $unitData['unitShort']], $skip))
) {
$diffIntervalArray[$nextIndex]['value'] += $unitData['value'] *
self::getFactorWithDefault($diffIntervalArray[$nextIndex]['unit'], $unitData['unit']);
$unitData['value'] = 0;
}
}
}

$transChoice = function ($short, $unitData, $index, $parts) use ($absolute, $handleDeclensions, $translator, $aUnit, $altNumbers, $interpolations) {
$count = $unitData['value'];

if ($short) {
$result = $handleDeclensions($unitData['unitShort'], $count, $index, $parts);

if ($result !== null) {
return $result;
}
} elseif ($aUnit) {
$result = $handleDeclensions('a_'.$unitData['unit'], $count, $index, $parts);

if ($result !== null) {
return $result;
}
}

if (!$absolute) {
return $handleDeclensions($unitData['unit'], $count, $index, $parts);
}

return $this->translate($unitData['unit'], $interpolations, $count, $translator, $altNumbers);
};

$fallbackUnit = ['second', 's'];

foreach ($diffIntervalArray as $diffIntervalData) {
if ($diffIntervalData['value'] > 0) {
$unit = $short ? $diffIntervalData['unitShort'] : $diffIntervalData['unit'];
$count = $diffIntervalData['value'];
$interval[] = [$short, $diffIntervalData];
} elseif ($options & CarbonInterface::SEQUENTIAL_PARTS_ONLY && \count($interval) > 0) {
break;
}


if (\count($interval) >= $parts) {
break;
}


if (\in_array($minimumUnit, [$diffIntervalData['unit'], $diffIntervalData['unitShort']], true)) {
$fallbackUnit = [$diffIntervalData['unit'], $diffIntervalData['unitShort']];

break;
}
}

$actualParts = \count($interval);

foreach ($interval as $index => &$item) {
$item = $transChoice($item[0], $item[1], $index, $actualParts);
}

if (\count($interval) === 0) {
if ($relativeToNow && $options & CarbonInterface::JUST_NOW) {
$key = 'diff_now';
$translation = $this->translate($key, $interpolations, null, $translator);

if ($translation !== $key) {
return $translation;
}
}

$count = $options & CarbonInterface::NO_ZERO_DIFF ? 1 : 0;
$unit = $fallbackUnit[$short ? 1 : 0];
$interval[] = $this->translate($unit, $interpolations, $count, $translator, $altNumbers);
}


$time = $join($interval);

unset($diffIntervalArray, $interval);

if ($absolute) {
return $time;
}

$isFuture = $this->invert === 1;

$transId = $relativeToNow ? ($isFuture ? 'from_now' : 'ago') : ($isFuture ? 'after' : 'before');

if ($parts === 1) {
if ($relativeToNow && $unit === 'day') {
$specialTranslations = static::SPECIAL_TRANSLATIONS[$count] ?? null;

if ($specialTranslations && $options & $specialTranslations['option']) {
$key = $specialTranslations[$isFuture ? 'future' : 'past'];
$translation = $this->translate($key, $interpolations, null, $translator);

if ($translation !== $key) {
return $translation;
}
}
}

$aTime = $aUnit ? $handleDeclensions('a_'.$unit, $count) : null;

$time = $aTime ?: $handleDeclensions($unit, $count) ?: $time;
}

$time = [':time' => $time];

return $this->translate($transId, array_merge($time, $interpolations, $time), null, $translator);
}

public function format(string $format): string
{
$output = parent::format($format);

if (!str_contains($format, '%a') || !isset($this->startDate, $this->endDate)) {
return $output;
}

$this->rawInterval ??= $this->startDate->diffAsDateInterval($this->endDate);

return str_replace('(unknown)', $this->rawInterval->format('%a'), $output);
}








public function __toString(): string
{
$format = $this->localToStringFormat
?? $this->getFactory()->getSettings()['toStringFormat']
?? null;

if (!$format) {
return $this->forHumans();
}

if ($format instanceof Closure) {
return $format($this);
}

return $this->format($format);
}











public function toDateInterval(): DateInterval
{
return self::castIntervalToClass($this, DateInterval::class);
}








public function toPeriod(...$params): CarbonPeriod
{
if ($this->timezoneSetting) {
$timeZone = \is_string($this->timezoneSetting)
? new DateTimeZone($this->timezoneSetting)
: $this->timezoneSetting;

if ($timeZone instanceof DateTimeZone) {
array_unshift($params, $timeZone);
}
}

$class = ($params[0] ?? null) instanceof DateTime ? CarbonPeriod::class : CarbonPeriodImmutable::class;

return $class::create($this, ...$params);
}









public function stepBy($interval, Unit|string|null $unit = null): CarbonPeriod
{
$this->checkStartAndEnd();
$start = $this->startDate ?? CarbonImmutable::make('now');
$end = $this->endDate ?? $start->copy()->add($this);

try {
$step = static::make($interval, $unit);
} catch (InvalidFormatException $exception) {
if ($unit || (\is_string($interval) ? preg_match('/(\s|\d)/', $interval) : !($interval instanceof Unit))) {
throw $exception;
}

$step = static::make(1, $interval);
}

$class = $start instanceof DateTime ? CarbonPeriod::class : CarbonPeriodImmutable::class;

return $class::create($step, $start, $end);
}









public function invert($inverted = null): static
{
$this->invert = (\func_num_args() === 0 ? !$this->invert : $inverted) ? 1 : 0;

return $this;
}

protected function solveNegativeInterval(): static
{
if (!$this->isEmpty() && $this->years <= 0 && $this->months <= 0 && $this->dayz <= 0 && $this->hours <= 0 && $this->minutes <= 0 && $this->seconds <= 0 && $this->microseconds <= 0) {
$this->years *= self::NEGATIVE;
$this->months *= self::NEGATIVE;
$this->dayz *= self::NEGATIVE;
$this->hours *= self::NEGATIVE;
$this->minutes *= self::NEGATIVE;
$this->seconds *= self::NEGATIVE;
$this->microseconds *= self::NEGATIVE;
$this->invert();
}

return $this;
}









public function add($unit, $value = 1): static
{
if (is_numeric($unit)) {
[$value, $unit] = [$unit, $value];
}

if (\is_string($unit) && !preg_match('/^\s*-?\d/', $unit)) {
$unit = "$value $unit";
$value = 1;
}

$interval = static::make($unit);

if (!$interval) {
throw new InvalidIntervalException('This type of data cannot be added/subtracted.');
}

if ($value !== 1) {
$interval->times($value);
}

$sign = ($this->invert === 1) !== ($interval->invert === 1) ? self::NEGATIVE : self::POSITIVE;
$this->years += $interval->y * $sign;
$this->months += $interval->m * $sign;
$this->dayz += ($interval->days === false ? $interval->d : $interval->days) * $sign;
$this->hours += $interval->h * $sign;
$this->minutes += $interval->i * $sign;
$this->seconds += $interval->s * $sign;
$this->microseconds += $interval->microseconds * $sign;

$this->solveNegativeInterval();

return $this;
}









public function sub($unit, $value = 1): static
{
if (is_numeric($unit)) {
[$value, $unit] = [$unit, $value];
}

return $this->add($unit, -(float) $value);
}









public function subtract($unit, $value = 1): static
{
return $this->sub($unit, $value);
}















public function plus(
$years = 0,
$months = 0,
$weeks = 0,
$days = 0,
$hours = 0,
$minutes = 0,
$seconds = 0,
$microseconds = 0
): static {
return $this->add("
            $years years $months months $weeks weeks $days days
            $hours hours $minutes minutes $seconds seconds $microseconds microseconds
        ");
}















public function minus(
$years = 0,
$months = 0,
$weeks = 0,
$days = 0,
$hours = 0,
$minutes = 0,
$seconds = 0,
$microseconds = 0
): static {
return $this->sub("
            $years years $months months $weeks weeks $days days
            $hours hours $minutes minutes $seconds seconds $microseconds microseconds
        ");
}
















public function times($factor): static
{
if ($factor < 0) {
$this->invert = $this->invert ? 0 : 1;
$factor = -$factor;
}

$this->years = (int) round($this->years * $factor);
$this->months = (int) round($this->months * $factor);
$this->dayz = (int) round($this->dayz * $factor);
$this->hours = (int) round($this->hours * $factor);
$this->minutes = (int) round($this->minutes * $factor);
$this->seconds = (int) round($this->seconds * $factor);
$this->microseconds = (int) round($this->microseconds * $factor);

return $this;
}
















public function shares($divider): static
{
return $this->times(1 / $divider);
}

protected function copyProperties(self $interval, $ignoreSign = false): static
{
$this->years = $interval->years;
$this->months = $interval->months;
$this->dayz = $interval->dayz;
$this->hours = $interval->hours;
$this->minutes = $interval->minutes;
$this->seconds = $interval->seconds;
$this->microseconds = $interval->microseconds;

if (!$ignoreSign) {
$this->invert = $interval->invert;
}

return $this;
}








public function multiply($factor): static
{
if ($factor < 0) {
$this->invert = $this->invert ? 0 : 1;
$factor = -$factor;
}

$yearPart = (int) floor($this->years * $factor); 

if ($yearPart) {
$this->years -= $yearPart / $factor;
}

return $this->copyProperties(
static::create($yearPart)
->microseconds(abs($this->totalMicroseconds) * $factor)
->cascade(),
true,
);
}








public function divide($divider): static
{
return $this->multiply(1 / $divider);
}








public static function getDateIntervalSpec(DateInterval $interval, bool $microseconds = false, array $skip = []): string
{
$date = array_filter([
static::PERIOD_YEARS => abs($interval->y),
static::PERIOD_MONTHS => abs($interval->m),
static::PERIOD_DAYS => abs($interval->d),
]);

$skip = array_map([Unit::class, 'toNameIfUnit'], $skip);

if (
$interval->days >= CarbonInterface::DAYS_PER_WEEK * CarbonInterface::WEEKS_PER_MONTH &&
(!isset($date[static::PERIOD_YEARS]) || \count(array_intersect(['y', 'year', 'years'], $skip))) &&
(!isset($date[static::PERIOD_MONTHS]) || \count(array_intersect(['m', 'month', 'months'], $skip)))
) {
$date = [
static::PERIOD_DAYS => abs($interval->days),
];
}

$seconds = abs($interval->s);
if ($microseconds && $interval->f > 0) {
$seconds = \sprintf('%d.%06d', $seconds, abs($interval->f) * 1000000);
}

$time = array_filter([
static::PERIOD_HOURS => abs($interval->h),
static::PERIOD_MINUTES => abs($interval->i),
static::PERIOD_SECONDS => $seconds,
]);

$specString = static::PERIOD_PREFIX;

foreach ($date as $key => $value) {
$specString .= $value.$key;
}

if (\count($time) > 0) {
$specString .= static::PERIOD_TIME_PREFIX;
foreach ($time as $key => $value) {
$specString .= $value.$key;
}
}

return $specString === static::PERIOD_PREFIX ? 'PT0S' : $specString;
}






public function spec(bool $microseconds = false): string
{
return static::getDateIntervalSpec($this, $microseconds);
}









public static function compareDateIntervals(DateInterval $first, DateInterval $second): int
{
$current = Carbon::now();
$passed = $current->avoidMutation()->add($second);
$current->add($first);

return $current <=> $passed;
}








public function compare(DateInterval $interval): int
{
return static::compareDateIntervals($this, $interval);
}






public function cascade(): static
{
return $this->doCascade(false);
}

public function hasNegativeValues(): bool
{
foreach ($this->toArray() as $value) {
if ($value < 0) {
return true;
}
}

return false;
}

public function hasPositiveValues(): bool
{
foreach ($this->toArray() as $value) {
if ($value > 0) {
return true;
}
}

return false;
}










public function total(string $unit): float
{
$realUnit = $unit = strtolower($unit);

if (\in_array($unit, ['days', 'weeks'])) {
$realUnit = 'dayz';
} elseif (!\in_array($unit, ['microseconds', 'milliseconds', 'seconds', 'minutes', 'hours', 'dayz', 'months', 'years'])) {
throw new UnknownUnitException($unit);
}

$this->checkStartAndEnd();

if ($this->startDate && $this->endDate) {
$diff = $this->startDate->diffInUnit($unit, $this->endDate);

return $this->absolute ? abs($diff) : $diff;
}

$result = 0;
$cumulativeFactor = 0;
$unitFound = false;
$factors = self::getFlipCascadeFactors();
$daysPerWeek = (int) static::getDaysPerWeek();

$values = [
'years' => $this->years,
'months' => $this->months,
'weeks' => (int) ($this->d / $daysPerWeek),
'dayz' => fmod($this->d, $daysPerWeek),
'hours' => $this->hours,
'minutes' => $this->minutes,
'seconds' => $this->seconds,
'milliseconds' => (int) ($this->microseconds / Carbon::MICROSECONDS_PER_MILLISECOND),
'microseconds' => $this->microseconds % Carbon::MICROSECONDS_PER_MILLISECOND,
];

if (isset($factors['dayz']) && $factors['dayz'][0] !== 'weeks') {
$values['dayz'] += $values['weeks'] * $daysPerWeek;
$values['weeks'] = 0;
}

foreach ($factors as $source => [$target, $factor]) {
if ($source === $realUnit) {
$unitFound = true;
$value = $values[$source];
$result += $value;
$cumulativeFactor = 1;
}

if ($factor === false) {
if ($unitFound) {
break;
}

$result = 0;
$cumulativeFactor = 0;

continue;
}

if ($target === $realUnit) {
$unitFound = true;
}

if ($cumulativeFactor) {
$cumulativeFactor *= $factor;
$result += $values[$target] * $cumulativeFactor;

continue;
}

$value = $values[$source];

$result = ($result + $value) / $factor;
}

if (isset($target) && !$cumulativeFactor) {
$result += $values[$target];
}

if (!$unitFound) {
throw new UnitNotConfiguredException($unit);
}

if ($this->invert) {
$result *= self::NEGATIVE;
}

if ($unit === 'weeks') {
$result /= $daysPerWeek;
}


return fmod($result, 1) === 0.0 ? (int) $result : $result;
}










public function eq($interval): bool
{
return $this->equalTo($interval);
}








public function equalTo($interval): bool
{
$interval = $this->resolveInterval($interval);

if ($interval === null) {
return false;
}

$step = $this->getStep();

if ($step) {
return $step === $interval->getStep();
}

if ($this->isEmpty()) {
return $interval->isEmpty();
}

$cascadedInterval = $this->copy()->cascade();
$comparedInterval = $interval->copy()->cascade();

return $cascadedInterval->invert === $comparedInterval->invert &&
$cascadedInterval->getNonZeroValues() === $comparedInterval->getNonZeroValues();
}










public function ne($interval): bool
{
return $this->notEqualTo($interval);
}








public function notEqualTo($interval): bool
{
return !$this->eq($interval);
}










public function gt($interval): bool
{
return $this->greaterThan($interval);
}








public function greaterThan($interval): bool
{
$interval = $this->resolveInterval($interval);

return $interval === null || $this->totalMicroseconds > $interval->totalMicroseconds;
}










public function gte($interval): bool
{
return $this->greaterThanOrEqualTo($interval);
}








public function greaterThanOrEqualTo($interval): bool
{
return $this->greaterThan($interval) || $this->equalTo($interval);
}










public function lt($interval): bool
{
return $this->lessThan($interval);
}








public function lessThan($interval): bool
{
$interval = $this->resolveInterval($interval);

return $interval !== null && $this->totalMicroseconds < $interval->totalMicroseconds;
}










public function lte($interval): bool
{
return $this->lessThanOrEqualTo($interval);
}








public function lessThanOrEqualTo($interval): bool
{
return $this->lessThan($interval) || $this->equalTo($interval);
}






















public function between($interval1, $interval2, bool $equal = true): bool
{
return $equal
? $this->greaterThanOrEqualTo($interval1) && $this->lessThanOrEqualTo($interval2)
: $this->greaterThan($interval1) && $this->lessThan($interval2);
}
















public function betweenIncluded($interval1, $interval2): bool
{
return $this->between($interval1, $interval2, true);
}
















public function betweenExcluded($interval1, $interval2): bool
{
return $this->between($interval1, $interval2, false);
}


















public function isBetween($interval1, $interval2, bool $equal = true): bool
{
return $this->between($interval1, $interval2, $equal);
}






public function roundUnit(string $unit, DateInterval|string|int|float $precision = 1, string $function = 'round'): static
{
if (static::getCascadeFactors() !== static::getDefaultCascadeFactors()) {
$value = $function($this->total($unit) / $precision) * $precision;
$inverted = $value < 0;

return $this->copyProperties(self::fromString(
number_format(abs($value), 12, '.', '').' '.$unit
)->invert($inverted)->cascade());
}

$base = CarbonImmutable::parse('2000-01-01 00:00:00', 'UTC')
->roundUnit($unit, $precision, $function);
$next = $base->add($this);
$inverted = $next < $base;

if ($inverted) {
$next = $base->sub($this);
}

$this->copyProperties(
$next
->roundUnit($unit, $precision, $function)
->diff($base),
);

return $this->invert($inverted);
}











public function floorUnit(string $unit, $precision = 1): static
{
return $this->roundUnit($unit, $precision, 'floor');
}











public function ceilUnit(string $unit, $precision = 1): static
{
return $this->roundUnit($unit, $precision, 'ceil');
}











public function round($precision = 1, string $function = 'round'): static
{
return $this->roundWith($precision, $function);
}








public function floor(DateInterval|string|float|int $precision = 1): static
{
return $this->round($precision, 'floor');
}








public function ceil(DateInterval|string|float|int $precision = 1): static
{
return $this->round($precision, 'ceil');
}

public function __unserialize(array $data): void
{
$properties = array_combine(
array_map(
static fn (mixed $key) => \is_string($key)
? str_replace('tzName', 'timezoneSetting', $key)
: $key,
array_keys($data),
),
$data,
);

if (method_exists(parent::class, '__unserialize')) {

parent::__unserialize($properties);

return;
}



$properties = array_combine(
array_map(
static fn (string $property) => preg_replace('/^\0.+\0/', '', $property),
array_keys($data),
),
$data,
);
$localStrictMode = $this->localStrictModeEnabled;
$this->localStrictModeEnabled = false;
$days = $properties['days'] ?? false;
$this->days = $days === false ? false : (int) $days;
$this->y = (int) ($properties['y'] ?? 0);
$this->m = (int) ($properties['m'] ?? 0);
$this->d = (int) ($properties['d'] ?? 0);
$this->h = (int) ($properties['h'] ?? 0);
$this->i = (int) ($properties['i'] ?? 0);
$this->s = (int) ($properties['s'] ?? 0);
$this->f = (float) ($properties['f'] ?? 0.0);

$this->weekday = (int) ($properties['weekday'] ?? 0);

$this->weekday_behavior = (int) ($properties['weekday_behavior'] ?? 0);

$this->first_last_day_of = (int) ($properties['first_last_day_of'] ?? 0);
$this->invert = (int) ($properties['invert'] ?? 0);

$this->special_type = (int) ($properties['special_type'] ?? 0);

$this->special_amount = (int) ($properties['special_amount'] ?? 0);

$this->have_weekday_relative = (int) ($properties['have_weekday_relative'] ?? 0);

$this->have_special_relative = (int) ($properties['have_special_relative'] ?? 0);
parent::__construct(self::getDateIntervalSpec($this));

foreach ($properties as $property => $value) {
if ($property === 'localStrictModeEnabled') {
continue;
}

$this->$property = $value;
}

$this->localStrictModeEnabled = $properties['localStrictModeEnabled'] ?? $localStrictMode;

}

/**
@template





*/
private static function withOriginal(mixed $interval, mixed $original): mixed
{
if ($interval instanceof self) {
$interval->originalInput = $original;
}

return $interval;
}

private static function standardizeUnit(string $unit): string
{
$unit = rtrim($unit, 'sz').'s';

return $unit === 'days' ? 'dayz' : $unit;
}

private static function getFlipCascadeFactors(): array
{
if (!self::$flipCascadeFactors) {
self::$flipCascadeFactors = [];

foreach (self::getCascadeFactors() as $to => [$factor, $from]) {
self::$flipCascadeFactors[self::standardizeUnit($from)] = [self::standardizeUnit($to), $factor];
}
}

return self::$flipCascadeFactors;
}

/**
@template
@psalm-param





*/
private static function castIntervalToClass(DateInterval $interval, string $className, array $skip = []): object
{
$mainClass = DateInterval::class;

if (!is_a($className, $mainClass, true)) {
throw new InvalidCastException("$className is not a sub-class of $mainClass.");
}

$microseconds = $interval->f;
$instance = self::buildInstance($interval, $className, $skip);

if ($instance instanceof self) {
$instance->originalInput = $interval;
}

if ($microseconds) {
$instance->f = $microseconds;
}

if ($interval instanceof self && is_a($className, self::class, true)) {
self::copyStep($interval, $instance);
}

self::copyNegativeUnits($interval, $instance);

return self::withOriginal($instance, $interval);
}

/**
@template
@psalm-param





*/
private static function buildInstance(
DateInterval $interval,
string $className,
array $skip = [],
): object {
$serialization = self::buildSerializationString($interval, $className, $skip);

return match ($serialization) {
null => new $className(static::getDateIntervalSpec($interval, false, $skip)),
default => unserialize($serialization),
};
}












private static function buildSerializationString(
DateInterval $interval,
string $className,
array $skip = [],
): ?string {
if ($interval->days === false || PHP_VERSION_ID < 8_02_00 || $skip !== []) {
return null;
}


if ($interval instanceof self && !is_a($className, self::class, true)) {
$interval = clone $interval;
unset($interval->timezoneSetting);
unset($interval->originalInput);
unset($interval->startDate);
unset($interval->endDate);
unset($interval->rawInterval);
unset($interval->absolute);
unset($interval->initialValues);
unset($interval->clock);
unset($interval->step);
unset($interval->localMonthsOverflow);
unset($interval->localYearsOverflow);
unset($interval->localStrictModeEnabled);
unset($interval->localHumanDiffOptions);
unset($interval->localToStringFormat);
unset($interval->localSerializer);
unset($interval->localMacros);
unset($interval->localGenericMacros);
unset($interval->localFormatFunction);
unset($interval->localTranslator);
}

$serialization = serialize($interval);
$inputClass = $interval::class;
$expectedStart = 'O:'.\strlen($inputClass).':"'.$inputClass.'":';

if (!str_starts_with($serialization, $expectedStart)) {
return null; 
}

return 'O:'.\strlen($className).':"'.$className.'":'.substr($serialization, \strlen($expectedStart));
}

private static function copyStep(self $from, self $to): void
{
$to->setStep($from->getStep());
}

private static function copyNegativeUnits(DateInterval $from, DateInterval $to): void
{
$to->invert = $from->invert;

foreach (['y', 'm', 'd', 'h', 'i', 's'] as $unit) {
if ($from->$unit < 0) {
self::setIntervalUnit($to, $unit, $to->$unit * self::NEGATIVE);
}
}
}

private function invertCascade(array $values): static
{
return $this->set(array_map(function ($value) {
return -$value;
}, $values))->doCascade(true)->invert();
}

private function doCascade(bool $deep): static
{
$originalData = $this->toArray();
$originalData['milliseconds'] = (int) ($originalData['microseconds'] / static::getMicrosecondsPerMillisecond());
$originalData['microseconds'] = $originalData['microseconds'] % static::getMicrosecondsPerMillisecond();
$originalData['weeks'] = (int) ($this->d / static::getDaysPerWeek());
$originalData['daysExcludeWeeks'] = fmod($this->d, static::getDaysPerWeek());
unset($originalData['days']);
$newData = $originalData;
$previous = [];

foreach (self::getFlipCascadeFactors() as $source => [$target, $factor]) {
foreach (['source', 'target'] as $key) {
if ($$key === 'dayz') {
$$key = 'daysExcludeWeeks';
}
}

$value = $newData[$source];
$modulo = fmod($factor + fmod($value, $factor), $factor);
$newData[$source] = $modulo;
$newData[$target] += ($value - $modulo) / $factor;

$decimalPart = fmod($newData[$source], 1);

if ($decimalPart !== 0.0) {
$unit = $source;

foreach ($previous as [$subUnit, $subFactor]) {
$newData[$unit] -= $decimalPart;
$newData[$subUnit] += $decimalPart * $subFactor;
$decimalPart = fmod($newData[$subUnit], 1);

if ($decimalPart === 0.0) {
break;
}

$unit = $subUnit;
}
}

array_unshift($previous, [$source, $factor]);
}

$positive = null;

if (!$deep) {
foreach ($newData as $value) {
if ($value) {
if ($positive === null) {
$positive = ($value > 0);

continue;
}

if (($value > 0) !== $positive) {
return $this->invertCascade($originalData)
->solveNegativeInterval();
}
}
}
}

return $this->set($newData)
->solveNegativeInterval();
}

private function needsDeclension(string $mode, int $index, int $parts): bool
{
return match ($mode) {
'last' => $index === $parts - 1,
default => true,
};
}

private function checkIntegerValue(string $name, mixed $value): void
{
if (\is_int($value)) {
return;
}

$this->assertSafeForInteger($name, $value);

if (\is_float($value) && (((float) (int) $value) === $value)) {
return;
}

if (!self::$floatSettersEnabled) {
$type = \gettype($value);
@trigger_error(
"Since 2.70.0, it's deprecated to pass $type value for $name.\n".
"It's truncated when stored as an integer interval unit.\n".
"From 3.0.0, decimal part will no longer be truncated and will be cascaded to smaller units.\n".
"- To maintain the current behavior, use explicit cast: $name((int) \$value)\n".
"- To adopt the new behavior globally, call CarbonInterval::enableFloatSetters()\n",
\E_USER_DEPRECATED,
);
}
}




private function assertSafeForInteger(string $name, mixed $value): void
{
if ($value && !\is_int($value) && ($value >= 0x7fffffffffffffff || $value <= -0x7fffffffffffffff)) {
throw new OutOfRangeException($name, -0x7fffffffffffffff, 0x7fffffffffffffff, $value);
}
}

private function handleDecimalPart(string $unit, mixed $value, mixed $integerValue): void
{
if (self::$floatSettersEnabled) {
$floatValue = (float) $value;
$base = (float) $integerValue;

if ($floatValue === $base) {
return;
}

$units = [
'y' => 'year',
'm' => 'month',
'd' => 'day',
'h' => 'hour',
'i' => 'minute',
's' => 'second',
];
$upper = true;

foreach ($units as $property => $name) {
if ($name === $unit) {
$upper = false;

continue;
}

if (!$upper && $this->$property !== 0) {
throw new RuntimeException(
"You cannot set $unit to a float value as $name would be overridden, ".
'set it first to 0 explicitly if you really want to erase its value'
);
}
}

$this->add($unit, $floatValue - $base);
}
}

private function getInnerValues(): array
{
return [$this->y, $this->m, $this->d, $this->h, $this->i, $this->s, $this->f, $this->invert, $this->days];
}

private function checkStartAndEnd(): void
{
if (
$this->initialValues !== null
&& ($this->startDate !== null || $this->endDate !== null)
&& $this->initialValues !== $this->getInnerValues()
) {
$this->absolute = false;
$this->startDate = null;
$this->endDate = null;
$this->rawInterval = null;
}
}


private function setSetting(string $setting, mixed $value): self
{
switch ($setting) {
case 'timezoneSetting':
return $value === null ? $this : $this->setTimezone($value);

case 'step':
$this->setStep($value);

return $this;

case 'localMonthsOverflow':
return $value === null ? $this : $this->settings(['monthOverflow' => $value]);

case 'localYearsOverflow':
return $value === null ? $this : $this->settings(['yearOverflow' => $value]);

case 'localStrictModeEnabled':
case 'localHumanDiffOptions':
case 'localToStringFormat':
case 'localSerializer':
case 'localMacros':
case 'localGenericMacros':
case 'localFormatFunction':
case 'localTranslator':
$this->$setting = $value;

return $this;

default:

return $this;
}
}

private static function incrementUnit(DateInterval $instance, string $unit, int $value): void
{
if ($value === 0) {
return;
}


if (PHP_VERSION_ID !== 8_03_20) {
$instance->$unit += $value;

return;
}


self::setIntervalUnit($instance, $unit, ($instance->$unit ?? 0) + $value);

}


private static function setIntervalUnit(DateInterval $instance, string $unit, mixed $value): void
{
switch ($unit) {
case 'y':
$instance->y = $value;

break;

case 'm':
$instance->m = $value;

break;

case 'd':
$instance->d = $value;

break;

case 'h':
$instance->h = $value;

break;

case 'i':
$instance->i = $value;

break;

case 's':
$instance->s = $value;

break;

default:
$instance->$unit = $value;
}
}
}
