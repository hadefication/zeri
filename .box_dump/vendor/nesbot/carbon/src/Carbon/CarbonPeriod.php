<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Exceptions\EndLessPeriodException;
use Carbon\Exceptions\InvalidCastException;
use Carbon\Exceptions\InvalidIntervalException;
use Carbon\Exceptions\InvalidPeriodDateException;
use Carbon\Exceptions\InvalidPeriodParameterException;
use Carbon\Exceptions\NotACarbonClassException;
use Carbon\Exceptions\NotAPeriodException;
use Carbon\Exceptions\UnknownGetterException;
use Carbon\Exceptions\UnknownMethodException;
use Carbon\Exceptions\UnreachableException;
use Carbon\Traits\DeprecatedPeriodProperties;
use Carbon\Traits\IntervalRounding;
use Carbon\Traits\LocalFactory;
use Carbon\Traits\Mixin;
use Carbon\Traits\Options;
use Carbon\Traits\ToStringFormat;
use Closure;
use Countable;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Generator;
use InvalidArgumentException;
use JsonSerializable;
use ReflectionException;
use ReturnTypeWillChange;
use RuntimeException;
use Throwable;


require PHP_VERSION < 8.2
? __DIR__.'/../../lazy/Carbon/ProtectedDatePeriod.php'
: __DIR__.'/../../lazy/Carbon/UnprotectedDatePeriod.php';


/**
@mixin






























































































































*/
class CarbonPeriod extends DatePeriodBase implements Countable, JsonSerializable
{
use LocalFactory;
use IntervalRounding;
use Mixin {
Mixin::mixin as baseMixin;
}
use Options {
Options::__debugInfo as baseDebugInfo;
}
use ToStringFormat;






public const RECURRENCES_FILTER = [self::class, 'filterRecurrences'];






public const END_DATE_FILTER = [self::class, 'filterEndDate'];






public const END_ITERATION = [self::class, 'endIteration'];






public const EXCLUDE_END_DATE = 8;






public const IMMUTABLE = 4;






public const NEXT_MAX_ATTEMPTS = 1000;






public const END_MAX_ATTEMPTS = 10000;






protected const DEFAULT_DATE_CLASS = Carbon::class;




protected static array $macros = [];




protected string $dateClass = Carbon::class;




protected ?CarbonInterval $dateInterval = null;




protected bool $constructed = false;




protected bool $isDefaultInterval = false;




protected array $filters = [];




protected ?CarbonInterface $startDate = null;




protected ?CarbonInterface $endDate = null;




protected int|float|null $carbonRecurrences = null;




protected ?int $options = null;





protected int $key = 0;





protected ?CarbonInterface $carbonCurrent = null;




protected ?DateTimeZone $timezone = null;




protected array|string|bool|null $validationResult = null;




protected DateTimeZone|string|int|null $timezoneSetting = null;

public function getIterator(): Generator
{
$this->rewind();

while ($this->valid()) {
$key = $this->key();
$value = $this->current();

yield $key => $value;

$this->next();
}
}




public static function make(mixed $var): ?static
{
try {
return static::instance($var);
} catch (NotAPeriodException) {
return static::create($var);
}
}




public static function instance(mixed $period): static
{
if ($period instanceof static) {
return $period->copy();
}

if ($period instanceof self) {
return new static(
$period->getStartDate(),
$period->getEndDate() ?? $period->getRecurrences(),
$period->getDateInterval(),
$period->getOptions(),
);
}

if ($period instanceof DatePeriod) {
return new static(
$period->start,
$period->end ?: ($period->recurrences - 1),
$period->interval,
$period->include_start_date ? 0 : static::EXCLUDE_START_DATE,
);
}

$class = static::class;
$type = \gettype($period);
$chunks = explode('::', __METHOD__);

throw new NotAPeriodException(
'Argument 1 passed to '.$class.'::'.end($chunks).'() '.
'must be an instance of DatePeriod or '.$class.', '.
($type === 'object' ? 'instance of '.\get_class($period) : $type).' given.',
);
}




public static function create(...$params): static
{
return static::createFromArray($params);
}




public static function createFromArray(array $params): static
{
return new static(...$params);
}




public static function createFromIso(string $iso, ?int $options = null): static
{
$params = static::parseIso8601($iso);

$instance = static::createFromArray($params);

$instance->options = ($instance instanceof CarbonPeriodImmutable ? static::IMMUTABLE : 0) | $options;
$instance->handleChangedParameters();

return $instance;
}

public static function createFromISO8601String(string $iso, ?int $options = null): static
{
return self::createFromIso($iso, $options);
}




protected static function intervalHasTime(DateInterval $interval): bool
{
return $interval->h || $interval->i || $interval->s || $interval->f;
}







protected static function isIso8601(mixed $var): bool
{
if (!\is_string($var)) {
return false;
}


$part = '[a-z]+(?:[_-][a-z]+)*';

preg_match("#\b$part/$part\b|(/)#i", $var, $match);

return isset($match[1]);
}






protected static function parseIso8601(string $iso): array
{
$result = [];

$interval = null;
$start = null;
$end = null;
$dateClass = static::DEFAULT_DATE_CLASS;

foreach (explode('/', $iso) as $key => $part) {
if ($key === 0 && preg_match('/^R(\d*|INF)$/', $part, $match)) {
$parsed = \strlen($match[1]) ? (($match[1] !== 'INF') ? (int) $match[1] : INF) : null;
} elseif ($interval === null && $parsed = self::makeInterval($part)) {
$interval = $part;
} elseif ($start === null && $parsed = $dateClass::make($part)) {
$start = $part;
} elseif ($end === null && $parsed = $dateClass::make(static::addMissingParts($start ?? '', $part))) {
$end = $part;
} else {
throw new InvalidPeriodParameterException("Invalid ISO 8601 specification: $iso.");
}

$result[] = $parsed;
}

return $result;
}




protected static function addMissingParts(string $source, string $target): string
{
$pattern = '/'.preg_replace('/\d+/', '[0-9]+', preg_quote($target, '/')).'$/';

$result = preg_replace($pattern, $target, $source, 1, $count);

return $count ? $result : $target;
}

private static function makeInterval(mixed $input): ?CarbonInterval
{
try {
return CarbonInterval::make($input);
} catch (Throwable) {
return null;
}
}

private static function makeTimezone(mixed $input): ?CarbonTimeZone
{
if (!\is_string($input)) {
return null;
}

try {
return CarbonTimeZone::create($input);
} catch (Throwable) {
return null;
}
}

/**
@param-closure-this












*/
public static function macro(string $name, ?callable $macro): void
{
static::$macros[$name] = $macro;
}































public static function mixin(object|string $mixin): void
{
static::baseMixin($mixin);
}




public static function hasMacro(string $name): bool
{
return isset(static::$macros[$name]);
}




public static function __callStatic(string $method, array $parameters): mixed
{
$date = new static();

if (static::hasMacro($method)) {
return static::bindMacroContext(null, static fn () => $date->callMacro($method, $parameters));
}

return $date->$method(...$parameters);
}








public function __construct(...$arguments)
{
$raw = null;

if (isset($arguments['raw'])) {
$raw = $arguments['raw'];
$this->isDefaultInterval = $arguments['isDefaultInterval'] ?? false;

if (isset($arguments['dateClass'])) {
$this->dateClass = $arguments['dateClass'];
}

$arguments = $raw;
}




$argumentsCount = \count($arguments);

if ($argumentsCount && static::isIso8601($iso = $arguments[0])) {
array_splice($arguments, 0, 1, static::parseIso8601($iso));
}

if ($argumentsCount === 1) {
if ($arguments[0] instanceof self) {
$arguments = [
$arguments[0]->getStartDate(),
$arguments[0]->getEndDate() ?? $arguments[0]->getRecurrences(),
$arguments[0]->getDateInterval(),
$arguments[0]->getOptions(),
];
} elseif ($arguments[0] instanceof DatePeriod) {
$arguments = [
$arguments[0]->start,
$arguments[0]->end ?: ($arguments[0]->recurrences - 1),
$arguments[0]->interval,
$arguments[0]->include_start_date ? 0 : static::EXCLUDE_START_DATE,
];
}
}

if (is_a($this->dateClass, DateTimeImmutable::class, true)) {
$this->options = static::IMMUTABLE;
}

$optionsSet = false;
$originalArguments = [];
$sortedArguments = [];

foreach ($arguments as $argument) {
$parsedDate = null;

if ($argument instanceof DateTimeZone) {
$sortedArguments = $this->configureTimezone($argument, $sortedArguments, $originalArguments);
} elseif (!isset($sortedArguments['interval']) &&
(
(\is_string($argument) && preg_match(
'/^(-?\d(\d(?![\/-])|[^\d\/-]([\/-])?)*|P[T\d].*|(?:\h*\d+(?:\.\d+)?\h*[a-z]+)+)$/i',
$argument,
)) ||
$argument instanceof DateInterval ||
$argument instanceof Closure ||
$argument instanceof Unit
) &&
$parsedInterval = self::makeInterval($argument)
) {
$sortedArguments['interval'] = $parsedInterval;
} elseif (!isset($sortedArguments['start']) && $parsedDate = $this->makeDateTime($argument)) {
$sortedArguments['start'] = $parsedDate;
$originalArguments['start'] = $argument;
} elseif (!isset($sortedArguments['end']) && ($parsedDate = $parsedDate ?? $this->makeDateTime($argument))) {
$sortedArguments['end'] = $parsedDate;
$originalArguments['end'] = $argument;
} elseif (!isset($sortedArguments['recurrences']) &&
!isset($sortedArguments['end']) &&
(\is_int($argument) || \is_float($argument))
&& $argument >= 0
) {
$sortedArguments['recurrences'] = $argument;
} elseif (!$optionsSet && (\is_int($argument) || $argument === null)) {
$optionsSet = true;
$sortedArguments['options'] = (((int) $this->options) | ((int) $argument));
} elseif ($parsedTimezone = self::makeTimezone($argument)) {
$sortedArguments = $this->configureTimezone($parsedTimezone, $sortedArguments, $originalArguments);
} else {
throw new InvalidPeriodParameterException('Invalid constructor parameters.');
}
}

if ($raw === null && isset($sortedArguments['start'])) {
$end = $sortedArguments['end'] ?? max(1, $sortedArguments['recurrences'] ?? 1);

if (\is_float($end)) {
$end = $end === INF ? PHP_INT_MAX : (int) round($end);
}

$raw = [
$sortedArguments['start'],
$sortedArguments['interval'] ?? CarbonInterval::day(),
$end,
];
}

$this->setFromAssociativeArray($sortedArguments);

if ($this->startDate === null) {
$dateClass = $this->dateClass;
$this->setStartDate($dateClass::now());
}

if ($this->dateInterval === null) {
$this->setDateInterval(CarbonInterval::day());

$this->isDefaultInterval = true;
}

if ($this->options === null) {
$this->setOptions(0);
}

parent::__construct(
$this->startDate,
$this->dateInterval,
$this->endDate ?? max(1, min(2147483639, $this->recurrences ?? 1)),
$this->options,
);

$this->constructed = true;
}




public function copy(): static
{
return clone $this;
}





protected function copyIfImmutable(): static
{
return $this;
}




protected function getGetter(string $name): ?callable
{
return match (strtolower(preg_replace('/[A-Z]/', '_$0', $name))) {
'start', 'start_date' => [$this, 'getStartDate'],
'end', 'end_date' => [$this, 'getEndDate'],
'interval', 'date_interval' => [$this, 'getDateInterval'],
'recurrences' => [$this, 'getRecurrences'],
'include_start_date' => [$this, 'isStartIncluded'],
'include_end_date' => [$this, 'isEndIncluded'],
'current' => [$this, 'current'],
'locale' => [$this, 'locale'],
'tzname', 'tz_name' => fn () => match (true) {
$this->timezoneSetting === null => null,
\is_string($this->timezoneSetting) => $this->timezoneSetting,
$this->timezoneSetting instanceof DateTimeZone => $this->timezoneSetting->getName(),
default => CarbonTimeZone::instance($this->timezoneSetting)->getName(),
},
default => null,
};
}








public function get(string $name)
{
$getter = $this->getGetter($name);

if ($getter) {
return $getter();
}

throw new UnknownGetterException($name);
}








public function __get(string $name)
{
return $this->get($name);
}








public function __isset(string $name): bool
{
return $this->getGetter($name) !== null;
}

/**
@alias




*/
public function clone()
{
return clone $this;
}








public function setDateClass(string $dateClass)
{
if (!is_a($dateClass, CarbonInterface::class, true)) {
throw new NotACarbonClassException($dateClass);
}

$self = $this->copyIfImmutable();
$self->dateClass = $dateClass;

if (is_a($dateClass, Carbon::class, true)) {
$self->options = $self->options & ~static::IMMUTABLE;
} elseif (is_a($dateClass, CarbonImmutable::class, true)) {
$self->options = $self->options | static::IMMUTABLE;
}

return $self;
}






public function getDateClass(): string
{
return $this->dateClass;
}











public function setDateInterval(mixed $interval, Unit|string|null $unit = null): static
{
if ($interval instanceof Unit) {
$interval = $interval->interval();
}

if ($unit instanceof Unit) {
$unit = $unit->name;
}

if (!$interval = CarbonInterval::make($interval, $unit)) {
throw new InvalidIntervalException('Invalid interval.');
}

if ($interval->spec() === 'PT0S' && !$interval->f && !$interval->getStep()) {
throw new InvalidIntervalException('Empty interval is not accepted.');
}

$self = $this->copyIfImmutable();
$self->dateInterval = $interval;

$self->isDefaultInterval = false;

$self->handleChangedParameters();

return $self;
}







public function resetDateInterval(): static
{
$self = $this->copyIfImmutable();
$self->setDateInterval(CarbonInterval::day());

$self->isDefaultInterval = true;

return $self;
}




public function invertDateInterval(): static
{
return $this->setDateInterval($this->dateInterval->invert());
}









public function setDates(mixed $start, mixed $end): static
{
return $this->setStartDate($start)->setEndDate($end);
}








public function setOptions(?int $options): static
{
$self = $this->copyIfImmutable();
$self->options = $options ?? 0;

$self->handleChangedParameters();

return $self;
}




public function getOptions(): int
{
return $this->options ?? 0;
}











public function toggleOptions(int $options, ?bool $state = null): static
{
$self = $this->copyIfImmutable();

if ($state === null) {
$state = ($this->options & $options) !== $options;
}

return $self->setOptions(
$state ?
$this->options | $options :
$this->options & ~$options,
);
}




public function excludeStartDate(bool $state = true): static
{
return $this->toggleOptions(static::EXCLUDE_START_DATE, $state);
}




public function excludeEndDate(bool $state = true): static
{
return $this->toggleOptions(static::EXCLUDE_END_DATE, $state);
}




public function getDateInterval(): CarbonInterval
{
return $this->dateInterval->copy();
}






public function getStartDate(?string $rounding = null): CarbonInterface
{
$date = $this->startDate->avoidMutation();

return $rounding ? $date->round($this->getDateInterval(), $rounding) : $date;
}






public function getEndDate(?string $rounding = null): ?CarbonInterface
{
if (!$this->endDate) {
return null;
}

$date = $this->endDate->avoidMutation();

return $rounding ? $date->round($this->getDateInterval(), $rounding) : $date;
}




#[ReturnTypeWillChange]
public function getRecurrences(): int|float|null
{
return $this->carbonRecurrences;
}




public function isStartExcluded(): bool
{
return ($this->options & static::EXCLUDE_START_DATE) !== 0;
}




public function isEndExcluded(): bool
{
return ($this->options & static::EXCLUDE_END_DATE) !== 0;
}




public function isStartIncluded(): bool
{
return !$this->isStartExcluded();
}




public function isEndIncluded(): bool
{
return !$this->isEndExcluded();
}




public function getIncludedStartDate(): CarbonInterface
{
$start = $this->getStartDate();

if ($this->isStartExcluded()) {
return $start->add($this->getDateInterval());
}

return $start;
}





public function getIncludedEndDate(): CarbonInterface
{
$end = $this->getEndDate();

if (!$end) {
return $this->calculateEnd();
}

if ($this->isEndExcluded()) {
return $end->sub($this->getDateInterval());
}

return $end;
}






public function addFilter(callable|string $callback, ?string $name = null): static
{
$self = $this->copyIfImmutable();
$tuple = $self->createFilterTuple(\func_get_args());

$self->filters[] = $tuple;

$self->handleChangedParameters();

return $self;
}






public function prependFilter(callable|string $callback, ?string $name = null): static
{
$self = $this->copyIfImmutable();
$tuple = $self->createFilterTuple(\func_get_args());

array_unshift($self->filters, $tuple);

$self->handleChangedParameters();

return $self;
}




public function removeFilter(callable|string $filter): static
{
$self = $this->copyIfImmutable();
$key = \is_callable($filter) ? 0 : 1;

$self->filters = array_values(array_filter(
$this->filters,
static fn ($tuple) => $tuple[$key] !== $filter,
));

$self->updateInternalState();

$self->handleChangedParameters();

return $self;
}




public function hasFilter(callable|string $filter): bool
{
$key = \is_callable($filter) ? 0 : 1;

foreach ($this->filters as $tuple) {
if ($tuple[$key] === $filter) {
return true;
}
}

return false;
}




public function getFilters(): array
{
return $this->filters;
}




public function setFilters(array $filters): static
{
$self = $this->copyIfImmutable();
$self->filters = $filters;

$self->updateInternalState();

$self->handleChangedParameters();

return $self;
}




public function resetFilters(): static
{
$self = $this->copyIfImmutable();
$self->filters = [];

if ($self->endDate !== null) {
$self->filters[] = [static::END_DATE_FILTER, null];
}

if ($self->carbonRecurrences !== null) {
$self->filters[] = [static::RECURRENCES_FILTER, null];
}

$self->handleChangedParameters();

return $self;
}






public function setRecurrences(int|float|null $recurrences): static
{
if ($recurrences === null) {
return $this->removeFilter(static::RECURRENCES_FILTER);
}

if ($recurrences < 0) {
throw new InvalidPeriodParameterException('Invalid number of recurrences.');
}


$self = $this->copyIfImmutable();
$self->carbonRecurrences = $recurrences === INF ? INF : (int) $recurrences;

if (!$self->hasFilter(static::RECURRENCES_FILTER)) {
return $self->addFilter(static::RECURRENCES_FILTER);
}

$self->handleChangedParameters();

return $self;
}











public function setStartDate(mixed $date, ?bool $inclusive = null): static
{
if (!$this->isInfiniteDate($date) && !($date = ([$this->dateClass, 'make'])($date, $this->timezone))) {
throw new InvalidPeriodDateException('Invalid start date.');
}

$self = $this->copyIfImmutable();
$self->startDate = $date;

if ($inclusive !== null) {
$self = $self->toggleOptions(static::EXCLUDE_START_DATE, !$inclusive);
}

return $self;
}











public function setEndDate(mixed $date, ?bool $inclusive = null): static
{
if ($date !== null && !$this->isInfiniteDate($date) && !$date = ([$this->dateClass, 'make'])($date, $this->timezone)) {
throw new InvalidPeriodDateException('Invalid end date.');
}

if (!$date) {
return $this->removeFilter(static::END_DATE_FILTER);
}

$self = $this->copyIfImmutable();
$self->endDate = $date;

if ($inclusive !== null) {
$self = $self->toggleOptions(static::EXCLUDE_END_DATE, !$inclusive);
}

if (!$self->hasFilter(static::END_DATE_FILTER)) {
return $self->addFilter(static::END_DATE_FILTER);
}

$self->handleChangedParameters();

return $self;
}




public function valid(): bool
{
return $this->validateCurrentDate() === true;
}




public function key(): ?int
{
return $this->valid()
? $this->key
: null;
}




public function current(): ?CarbonInterface
{
return $this->valid()
? $this->prepareForReturn($this->carbonCurrent)
: null;
}






public function next(): void
{
if ($this->carbonCurrent === null) {
$this->rewind();
}

if ($this->validationResult !== static::END_ITERATION) {
$this->key++;

$this->incrementCurrentDateUntilValid();
}
}












public function rewind(): void
{
$this->key = 0;
$this->carbonCurrent = ([$this->dateClass, 'make'])($this->startDate);
$settings = $this->getSettings();

if ($this->hasLocalTranslator()) {
$settings['locale'] = $this->getTranslatorLocale();
}

$this->carbonCurrent->settings($settings);
$this->timezone = static::intervalHasTime($this->dateInterval) ? $this->carbonCurrent->getTimezone() : null;

if ($this->timezone) {
$this->carbonCurrent = $this->carbonCurrent->utc();
}

$this->validationResult = null;

if ($this->isStartExcluded() || $this->validateCurrentDate() === false) {
$this->incrementCurrentDateUntilValid();
}
}








public function skip(int $count = 1): bool
{
for ($i = $count; $this->valid() && $i > 0; $i--) {
$this->next();
}

return $this->valid();
}




public function toIso8601String(): string
{
$parts = [];

if ($this->carbonRecurrences !== null) {
$parts[] = 'R'.$this->carbonRecurrences;
}

$parts[] = $this->startDate->toIso8601String();

if (!$this->isDefaultInterval) {
$parts[] = $this->dateInterval->spec();
}

if ($this->endDate !== null) {
$parts[] = $this->endDate->toIso8601String();
}

return implode('/', $parts);
}




public function toString(): string
{
$format = $this->localToStringFormat
?? $this->getFactory()->getSettings()['toStringFormat']
?? null;

if ($format instanceof Closure) {
return $format($this);
}

$translator = ([$this->dateClass, 'getTranslator'])();

$parts = [];

$format = $format ?? (
!$this->startDate->isStartOfDay() || ($this->endDate && !$this->endDate->isStartOfDay())
? 'Y-m-d H:i:s'
: 'Y-m-d'
);

if ($this->carbonRecurrences !== null) {
$parts[] = $this->translate('period_recurrences', [], $this->carbonRecurrences, $translator);
}

$parts[] = $this->translate('period_interval', [':interval' => $this->dateInterval->forHumans([
'join' => true,
])], null, $translator);

$parts[] = $this->translate('period_start_date', [':date' => $this->startDate->rawFormat($format)], null, $translator);

if ($this->endDate !== null) {
$parts[] = $this->translate('period_end_date', [':date' => $this->endDate->rawFormat($format)], null, $translator);
}

$result = implode(' ', $parts);

return mb_strtoupper(mb_substr($result, 0, 1)).mb_substr($result, 1);
}




public function spec(): string
{
return $this->toIso8601String();
}








public function cast(string $className): object
{
if (!method_exists($className, 'instance')) {
if (is_a($className, DatePeriod::class, true)) {
return new $className(
$this->rawDate($this->getStartDate()),
$this->getDateInterval(),
$this->getEndDate() ? $this->rawDate($this->getIncludedEndDate()) : $this->getRecurrences(),
$this->isStartExcluded() ? DatePeriod::EXCLUDE_START_DATE : 0,
);
}

throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
}

return $className::instance($this);
}









public function toDatePeriod(): DatePeriod
{
return $this->cast(DatePeriod::class);
}








public function isUnfilteredAndEndLess(): bool
{
foreach ($this->filters as $filter) {
switch ($filter) {
case [static::RECURRENCES_FILTER, null]:
if ($this->carbonRecurrences !== null && is_finite($this->carbonRecurrences)) {
return false;
}

break;

case [static::END_DATE_FILTER, null]:
if ($this->endDate !== null && !$this->endDate->isEndOfTime()) {
return false;
}

break;

default:
return false;
}
}

return true;
}






public function toArray(): array
{
if ($this->isUnfilteredAndEndLess()) {
throw new EndLessPeriodException("Endless period can't be converted to array nor counted.");
}

$state = [
$this->key,
$this->carbonCurrent ? $this->carbonCurrent->avoidMutation() : null,
$this->validationResult,
];

$result = iterator_to_array($this);

[$this->key, $this->carbonCurrent, $this->validationResult] = $state;

return $result;
}




public function count(): int
{
return \count($this->toArray());
}




public function first(): ?CarbonInterface
{
if ($this->isUnfilteredAndEndLess()) {
foreach ($this as $date) {
$this->rewind();

return $date;
}

return null;
}

return ($this->toArray() ?: [])[0] ?? null;
}




public function last(): ?CarbonInterface
{
$array = $this->toArray();

return $array ? $array[\count($array) - 1] : null;
}




public function __toString(): string
{
return $this->toString();
}











public function __call(string $method, array $parameters): mixed
{
if (static::hasMacro($method)) {
return static::bindMacroContext($this, fn () => $this->callMacro($method, $parameters));
}

$roundedValue = $this->callRoundMethod($method, $parameters);

if ($roundedValue !== null) {
return $roundedValue;
}

$count = \count($parameters);

switch ($method) {
case 'start':
case 'since':
if ($count === 0) {
return $this->getStartDate();
}

self::setDefaultParameters($parameters, [
[0, 'date', null],
]);

return $this->setStartDate(...$parameters);

case 'sinceNow':
return $this->setStartDate(new Carbon(), ...$parameters);

case 'end':
case 'until':
if ($count === 0) {
return $this->getEndDate();
}

self::setDefaultParameters($parameters, [
[0, 'date', null],
]);

return $this->setEndDate(...$parameters);

case 'untilNow':
return $this->setEndDate(new Carbon(), ...$parameters);

case 'dates':
case 'between':
self::setDefaultParameters($parameters, [
[0, 'start', null],
[1, 'end', null],
]);

return $this->setDates(...$parameters);

case 'recurrences':
case 'times':
if ($count === 0) {
return $this->getRecurrences();
}

self::setDefaultParameters($parameters, [
[0, 'recurrences', null],
]);

return $this->setRecurrences(...$parameters);

case 'options':
if ($count === 0) {
return $this->getOptions();
}

self::setDefaultParameters($parameters, [
[0, 'options', null],
]);

return $this->setOptions(...$parameters);

case 'toggle':
self::setDefaultParameters($parameters, [
[0, 'options', null],
]);

return $this->toggleOptions(...$parameters);

case 'filter':
case 'push':
return $this->addFilter(...$parameters);

case 'prepend':
return $this->prependFilter(...$parameters);

case 'filters':
if ($count === 0) {
return $this->getFilters();
}

self::setDefaultParameters($parameters, [
[0, 'filters', []],
]);

return $this->setFilters(...$parameters);

case 'interval':
case 'each':
case 'every':
case 'step':
case 'stepBy':
if ($count === 0) {
return $this->getDateInterval();
}

return $this->setDateInterval(...$parameters);

case 'invert':
return $this->invertDateInterval();

case 'years':
case 'year':
case 'months':
case 'month':
case 'weeks':
case 'week':
case 'days':
case 'dayz':
case 'day':
case 'hours':
case 'hour':
case 'minutes':
case 'minute':
case 'seconds':
case 'second':
case 'milliseconds':
case 'millisecond':
case 'microseconds':
case 'microsecond':
return $this->setDateInterval((

[$this->isDefaultInterval ? new CarbonInterval('PT0S') : $this->dateInterval, $method]
)(...$parameters));
}

$dateClass = $this->dateClass;

if ($this->localStrictModeEnabled ?? $dateClass::isStrictModeEnabled()) {
throw new UnknownMethodException($method);
}

return $this;
}




public function setTimezone(DateTimeZone|string|int $timezone): static
{
$self = $this->copyIfImmutable();
$self->timezoneSetting = $timezone;
$self->timezone = CarbonTimeZone::instance($timezone);

if ($self->startDate) {
$self = $self->setStartDate($self->startDate->setTimezone($timezone));
}

if ($self->endDate) {
$self = $self->setEndDate($self->endDate->setTimezone($timezone));
}

return $self;
}




public function shiftTimezone(DateTimeZone|string|int $timezone): static
{
$self = $this->copyIfImmutable();
$self->timezoneSetting = $timezone;
$self->timezone = CarbonTimeZone::instance($timezone);

if ($self->startDate) {
$self = $self->setStartDate($self->startDate->shiftTimezone($timezone));
}

if ($self->endDate) {
$self = $self->setEndDate($self->endDate->shiftTimezone($timezone));
}

return $self;
}








public function calculateEnd(?string $rounding = null): CarbonInterface
{
if ($end = $this->getEndDate($rounding)) {
return $end;
}

if ($this->dateInterval->isEmpty()) {
return $this->getStartDate($rounding);
}

$date = $this->getEndFromRecurrences() ?? $this->iterateUntilEnd();

if ($date && $rounding) {
$date = $date->avoidMutation()->round($this->getDateInterval(), $rounding);
}

return $date;
}

private function getEndFromRecurrences(): ?CarbonInterface
{
if ($this->carbonRecurrences === null) {
throw new UnreachableException(
"Could not calculate period end without either explicit end or recurrences.\n".
"If you're looking for a forever-period, use ->setRecurrences(INF).",
);
}

if ($this->carbonRecurrences === INF) {
$start = $this->getStartDate();

return $start < $start->avoidMutation()->add($this->getDateInterval())
? CarbonImmutable::endOfTime()
: CarbonImmutable::startOfTime();
}

if ($this->filters === [[static::RECURRENCES_FILTER, null]]) {
return $this->getStartDate()->avoidMutation()->add(
$this->getDateInterval()->times(
$this->carbonRecurrences - ($this->isStartExcluded() ? 0 : 1),
),
);
}

return null;
}

private function iterateUntilEnd(): ?CarbonInterface
{
$attempts = 0;
$date = null;

foreach ($this as $date) {
if (++$attempts > static::END_MAX_ATTEMPTS) {
throw new UnreachableException(
'Could not calculate period end after iterating '.static::END_MAX_ATTEMPTS.' times.',
);
}
}

return $date;
}










public function overlaps(mixed $rangeOrRangeStart, mixed $rangeEnd = null): bool
{
$range = $rangeEnd ? static::create($rangeOrRangeStart, $rangeEnd) : $rangeOrRangeStart;

if (!($range instanceof self)) {
$range = static::create($range);
}

[$start, $end] = $this->orderCouple($this->getStartDate(), $this->calculateEnd());
[$rangeStart, $rangeEnd] = $this->orderCouple($range->getStartDate(), $range->calculateEnd());

return $end > $rangeStart && $rangeEnd > $start;
}











public function forEach(callable $callback): void
{
foreach ($this as $date) {
$callback($date);
}
}












public function map(callable $callback): Generator
{
foreach ($this as $date) {
yield $callback($date);
}
}







public function eq(mixed $period): bool
{
return $this->equalTo($period);
}





public function equalTo(mixed $period): bool
{
if (!($period instanceof self)) {
$period = self::make($period);
}

$end = $this->getEndDate();

return $period !== null
&& $this->getDateInterval()->eq($period->getDateInterval())
&& $this->getStartDate()->eq($period->getStartDate())
&& ($end ? $end->eq($period->getEndDate()) : $this->getRecurrences() === $period->getRecurrences())
&& ($this->getOptions() & (~static::IMMUTABLE)) === ($period->getOptions() & (~static::IMMUTABLE));
}







public function ne(mixed $period): bool
{
return $this->notEqualTo($period);
}





public function notEqualTo(mixed $period): bool
{
return !$this->eq($period);
}





public function startsBefore(mixed $date = null): bool
{
return $this->getStartDate()->lessThan($this->resolveCarbon($date));
}





public function startsBeforeOrAt(mixed $date = null): bool
{
return $this->getStartDate()->lessThanOrEqualTo($this->resolveCarbon($date));
}





public function startsAfter(mixed $date = null): bool
{
return $this->getStartDate()->greaterThan($this->resolveCarbon($date));
}





public function startsAfterOrAt(mixed $date = null): bool
{
return $this->getStartDate()->greaterThanOrEqualTo($this->resolveCarbon($date));
}





public function startsAt(mixed $date = null): bool
{
return $this->getStartDate()->equalTo($this->resolveCarbon($date));
}





public function endsBefore(mixed $date = null): bool
{
return $this->calculateEnd()->lessThan($this->resolveCarbon($date));
}





public function endsBeforeOrAt(mixed $date = null): bool
{
return $this->calculateEnd()->lessThanOrEqualTo($this->resolveCarbon($date));
}





public function endsAfter(mixed $date = null): bool
{
return $this->calculateEnd()->greaterThan($this->resolveCarbon($date));
}





public function endsAfterOrAt(mixed $date = null): bool
{
return $this->calculateEnd()->greaterThanOrEqualTo($this->resolveCarbon($date));
}





public function endsAt(mixed $date = null): bool
{
return $this->calculateEnd()->equalTo($this->resolveCarbon($date));
}





public function isStarted(): bool
{
return $this->startsBeforeOrAt();
}





public function isEnded(): bool
{
return $this->endsBeforeOrAt();
}





public function isInProgress(): bool
{
return $this->isStarted() && !$this->isEnded();
}




public function roundUnit(
string $unit,
DateInterval|float|int|string|null $precision = 1,
callable|string $function = 'round',
): static {
$self = $this->copyIfImmutable();
$self = $self->setStartDate($self->getStartDate()->roundUnit($unit, $precision, $function));

if ($self->endDate) {
$self = $self->setEndDate($self->getEndDate()->roundUnit($unit, $precision, $function));
}

return $self->setDateInterval($self->getDateInterval()->roundUnit($unit, $precision, $function));
}




public function floorUnit(string $unit, DateInterval|float|int|string|null $precision = 1): static
{
return $this->roundUnit($unit, $precision, 'floor');
}




public function ceilUnit(string $unit, DateInterval|float|int|string|null $precision = 1): static
{
return $this->roundUnit($unit, $precision, 'ceil');
}




public function round(
DateInterval|float|int|string|null $precision = null,
callable|string $function = 'round',
): static {
return $this->roundWith(
$precision ?? $this->getDateInterval()->setLocalTranslator(TranslatorImmutable::get('en'))->forHumans(),
$function
);
}




public function floor(DateInterval|float|int|string|null $precision = null): static
{
return $this->round($precision, 'floor');
}




public function ceil(DateInterval|float|int|string|null $precision = null): static
{
return $this->round($precision, 'ceil');
}








public function jsonSerialize(): array
{
return $this->toArray();
}




public function contains(mixed $date = null): bool
{
$startMethod = 'startsBefore'.($this->isStartIncluded() ? 'OrAt' : '');
$endMethod = 'endsAfter'.($this->isEndIncluded() ? 'OrAt' : '');

return $this->$startMethod($date) && $this->$endMethod($date);
}






public function follows(mixed $period, mixed ...$arguments): bool
{
$period = $this->resolveCarbonPeriod($period, ...$arguments);

return $this->getIncludedStartDate()->equalTo($period->getIncludedEndDate()->add($period->getDateInterval()));
}






public function isFollowedBy(mixed $period, mixed ...$arguments): bool
{
$period = $this->resolveCarbonPeriod($period, ...$arguments);

return $period->follows($this);
}







public function isConsecutiveWith(mixed $period, mixed ...$arguments): bool
{
return $this->follows($period, ...$arguments) || $this->isFollowedBy($period, ...$arguments);
}

public function __debugInfo(): array
{
$info = $this->baseDebugInfo();
unset(
$info['start'],
$info['end'],
$info['interval'],
$info['include_start_date'],
$info['include_end_date'],
$info['constructed'],
$info["\0*\0constructed"],
);

return $info;
}

public function __unserialize(array $data): void
{
try {
$values = array_combine(
array_map(
static fn (string $key): string => preg_replace('/^\0\*\0/', '', $key),
array_keys($data),
),
$data,
);

$this->initializeSerialization($values);

foreach ($values as $key => $value) {
if ($value === null) {
continue;
}

$property = match ($key) {
'tzName' => $this->setTimezone(...),
'options' => $this->setOptions(...),
'recurrences' => $this->setRecurrences(...),
'current' => function (mixed $current): void {
if (!($current instanceof CarbonInterface)) {
$current = $this->resolveCarbon($current);
}

$this->carbonCurrent = $current;
},
'start' => 'startDate',
'interval' => $this->setDateInterval(...),
'end' => 'endDate',
'key' => null,
'include_start_date' => function (bool $included): void {
$this->excludeStartDate(!$included);
},
'include_end_date' => function (bool $included): void {
$this->excludeEndDate(!$included);
},
default => $key,
};

if ($property === null) {
continue;
}

if (\is_callable($property)) {
$property($value);

continue;
}

if ($value instanceof DateTimeInterface && !($value instanceof CarbonInterface)) {
$value = ($value instanceof DateTime)
? Carbon::instance($value)
: CarbonImmutable::instance($value);
}

try {
$this->$property = $value;
} catch (Throwable) {

}
}

if (\array_key_exists('carbonRecurrences', $values)) {
$this->carbonRecurrences = $values['carbonRecurrences'];
} elseif (((int) ($values['recurrences'] ?? 0)) <= 1 && $this->endDate !== null) {
$this->carbonRecurrences = null;
}
} catch (Throwable $e) {

if (!method_exists(parent::class, '__unserialize')) {
throw $e;
}

parent::__unserialize($data);

}
}




protected function updateInternalState(): void
{
if (!$this->hasFilter(static::END_DATE_FILTER)) {
$this->endDate = null;
}

if (!$this->hasFilter(static::RECURRENCES_FILTER)) {
$this->carbonRecurrences = null;
}
}






protected function createFilterTuple(array $parameters): array
{
$method = array_shift($parameters);

if (!$this->isCarbonPredicateMethod($method)) {
return [$method, array_shift($parameters)];
}

return [static fn ($date) => ([$date, $method])(...$parameters), $method];
}





protected function isCarbonPredicateMethod(callable|string $callable): bool
{
return \is_string($callable) && str_starts_with($callable, 'is') &&
(method_exists($this->dateClass, $callable) || ([$this->dateClass, 'hasMacro'])($callable));
}






protected function filterRecurrences(CarbonInterface $current, int $key): bool|callable
{
if ($key < $this->carbonRecurrences) {
return true;
}

return static::END_ITERATION;
}






protected function filterEndDate(CarbonInterface $current): bool|callable
{
if (!$this->isEndExcluded() && $current == $this->endDate) {
return true;
}

if ($this->dateInterval->invert ? $current > $this->endDate : $current < $this->endDate) {
return true;
}

return static::END_ITERATION;
}






protected function endIteration(): callable
{
return static::END_ITERATION;
}




protected function handleChangedParameters(): void
{
if (($this->getOptions() & static::IMMUTABLE) && $this->dateClass === Carbon::class) {
$this->dateClass = CarbonImmutable::class;
} elseif (!($this->getOptions() & static::IMMUTABLE) && $this->dateClass === CarbonImmutable::class) {
$this->dateClass = Carbon::class;
}

$this->validationResult = null;
}









protected function validateCurrentDate(): bool|callable
{
if ($this->carbonCurrent === null) {
$this->rewind();
}


return $this->validationResult ?? ($this->validationResult = $this->checkFilters());
}






protected function checkFilters(): bool|callable
{
$current = $this->prepareForReturn($this->carbonCurrent);

foreach ($this->filters as $tuple) {
$result = \call_user_func($tuple[0], $current->avoidMutation(), $this->key, $this);

if ($result === static::END_ITERATION) {
return static::END_ITERATION;
}

if (!$result) {
return false;
}
}

return true;
}








protected function prepareForReturn(CarbonInterface $date)
{
$date = ([$this->dateClass, 'make'])($date);

if ($this->timezone) {
return $date->setTimezone($this->timezone);
}

return $date;
}






protected function incrementCurrentDateUntilValid(): void
{
$attempts = 0;

do {
$this->carbonCurrent = $this->carbonCurrent->add($this->dateInterval);

$this->validationResult = null;

if (++$attempts > static::NEXT_MAX_ATTEMPTS) {
throw new UnreachableException('Could not find next valid date.');
}
} while ($this->validateCurrentDate() === false);
}




protected function callMacro(string $name, array $parameters): mixed
{
$macro = static::$macros[$name];

if ($macro instanceof Closure) {
$boundMacro = @$macro->bindTo($this, static::class) ?: @$macro->bindTo(null, static::class);

return ($boundMacro ?: $macro)(...$parameters);
}

return $macro(...$parameters);
}









protected function resolveCarbon($date = null)
{
return $this->getStartDate()->nowWithSameTz()->carbonize($date);
}




protected function resolveCarbonPeriod(mixed $period, mixed ...$arguments): self
{
if ($period instanceof self) {
return $period;
}

return $period instanceof DatePeriod
? static::instance($period)
: static::create($period, ...$arguments);
}

private function orderCouple($first, $second): array
{
return $first > $second ? [$second, $first] : [$first, $second];
}

private function makeDateTime($value): ?DateTimeInterface
{
if ($value instanceof DateTimeInterface) {
return $value;
}

if ($value instanceof WeekDay || $value instanceof Month) {
$dateClass = $this->dateClass;

return new $dateClass($value, $this->timezoneSetting);
}

if (\is_string($value)) {
$value = trim($value);

if (!preg_match('/^P[\dT]/', $value) &&
!preg_match('/^R\d/', $value) &&
preg_match('/[a-z\d]/i', $value)
) {
$dateClass = $this->dateClass;

return $dateClass::parse($value, $this->timezoneSetting);
}
}

return null;
}

private function isInfiniteDate($date): bool
{
return $date instanceof CarbonInterface && ($date->isEndOfTime() || $date->isStartOfTime());
}

private function rawDate($date): ?DateTimeInterface
{
if ($date === false || $date === null) {
return null;
}

if ($date instanceof CarbonInterface) {
return $date->isMutable()
? $date->toDateTime()
: $date->toDateTimeImmutable();
}

if (\in_array(\get_class($date), [DateTime::class, DateTimeImmutable::class], true)) {
return $date;
}

$class = $date instanceof DateTime ? DateTime::class : DateTimeImmutable::class;

return new $class($date->format('Y-m-d H:i:s.u'), $date->getTimezone());
}

private static function setDefaultParameters(array &$parameters, array $defaults): void
{
foreach ($defaults as [$index, $name, $value]) {
if (!\array_key_exists($index, $parameters) && !\array_key_exists($name, $parameters)) {
$parameters[$index] = $value;
}
}
}

private function setFromAssociativeArray(array $parameters): void
{
if (isset($parameters['start'])) {
$this->setStartDate($parameters['start']);
}

if (isset($parameters['start'])) {
$this->setStartDate($parameters['start']);
}

if (isset($parameters['end'])) {
$this->setEndDate($parameters['end']);
}

if (isset($parameters['recurrences'])) {
$this->setRecurrences($parameters['recurrences']);
}

if (isset($parameters['interval'])) {
$this->setDateInterval($parameters['interval']);
}

if (isset($parameters['options'])) {
$this->setOptions($parameters['options']);
}
}

private function configureTimezone(DateTimeZone $timezone, array $sortedArguments, array $originalArguments): array
{
$this->setTimezone($timezone);

if (\is_string($originalArguments['start'] ?? null)) {
$sortedArguments['start'] = $this->makeDateTime($originalArguments['start']);
}

if (\is_string($originalArguments['end'] ?? null)) {
$sortedArguments['end'] = $this->makeDateTime($originalArguments['end']);
}

return $sortedArguments;
}

private function initializeSerialization(array $values): void
{
$serializationBase = [
'start' => $values['start'] ?? $values['startDate'] ?? null,
'current' => $values['current'] ?? $values['carbonCurrent'] ?? null,
'end' => $values['end'] ?? $values['endDate'] ?? null,
'interval' => $values['interval'] ?? $values['dateInterval'] ?? null,
'recurrences' => max(1, (int) ($values['recurrences'] ?? $values['carbonRecurrences'] ?? 1)),
'include_start_date' => $values['include_start_date'] ?? true,
'include_end_date' => $values['include_end_date'] ?? false,
];

foreach (['start', 'current', 'end'] as $dateProperty) {
if ($serializationBase[$dateProperty] instanceof Carbon) {
$serializationBase[$dateProperty] = $serializationBase[$dateProperty]->toDateTime();
} elseif ($serializationBase[$dateProperty] instanceof CarbonInterface) {
$serializationBase[$dateProperty] = $serializationBase[$dateProperty]->toDateTimeImmutable();
}
}

if ($serializationBase['interval'] instanceof CarbonInterval) {
$serializationBase['interval'] = $serializationBase['interval']->toDateInterval();
}


if (method_exists(parent::class, '__unserialize')) {
parent::__unserialize($serializationBase);

return;
}

$excludeStart = !($values['include_start_date'] ?? true);
$includeEnd = $values['include_end_date'] ?? true;

parent::__construct(
$serializationBase['start'],
$serializationBase['interval'],
$serializationBase['end'] ?? $serializationBase['recurrences'],
($excludeStart ? self::EXCLUDE_START_DATE : 0) | ($includeEnd && \defined('DatePeriod::INCLUDE_END_DATE') ? self::INCLUDE_END_DATE : 0),
);

}
}
