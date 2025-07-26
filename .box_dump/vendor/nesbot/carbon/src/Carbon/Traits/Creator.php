<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidDateException;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Carbon\Exceptions\OutOfRangeException;
use Carbon\Exceptions\UnitException;
use Carbon\Month;
use Carbon\Translator;
use Carbon\WeekDay;
use Closure;
use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use ReturnTypeWillChange;
use Symfony\Contracts\Translation\TranslatorInterface;










trait Creator
{
use ObjectInitialisation;
use LocalFactory;




protected static ?array $lastErrors = null;









public function __construct(
DateTimeInterface|WeekDay|Month|string|int|float|null $time = null,
DateTimeZone|string|int|null $timezone = null,
) {
$this->initLocalFactory();

if ($time instanceof Month) {
$time = $time->name.' 1';
} elseif ($time instanceof WeekDay) {
$time = $time->name;
} elseif ($time instanceof DateTimeInterface) {
$time = $this->constructTimezoneFromDateTime($time, $timezone)->format('Y-m-d H:i:s.u');
}

if (\is_string($time) && str_starts_with($time, '@')) {
$time = static::createFromTimestampUTC(substr($time, 1))->format('Y-m-d\TH:i:s.uP');
} elseif (is_numeric($time) && (!\is_string($time) || !preg_match('/^\d{1,14}$/', $time))) {
$time = static::createFromTimestampUTC($time)->format('Y-m-d\TH:i:s.uP');
}



$isNow = \in_array($time, [null, '', 'now'], true);
$timezone = static::safeCreateDateTimeZone($timezone) ?? null;

if (
($this->clock || (
method_exists(static::class, 'hasTestNow') &&
method_exists(static::class, 'getTestNow') &&
static::hasTestNow()
)) &&
($isNow || static::hasRelativeKeywords($time))
) {
$this->mockConstructorParameters($time, $timezone);
}

try {
parent::__construct($time ?? 'now', $timezone);
} catch (Exception $exception) {
throw new InvalidFormatException($exception->getMessage(), 0, $exception);
}

$this->constructedObjectId = spl_object_hash($this);

self::setLastErrors(parent::getLastErrors());
}




private function constructTimezoneFromDateTime(
DateTimeInterface $date,
DateTimeZone|string|int|null &$timezone,
): DateTimeInterface {
if ($timezone !== null) {
$safeTz = static::safeCreateDateTimeZone($timezone);

if ($safeTz) {
$date = ($date instanceof DateTimeImmutable ? $date : clone $date)->setTimezone($safeTz);
}

return $date;
}

$timezone = $date->getTimezone();

return $date;
}




public function __clone(): void
{
$this->constructedObjectId = spl_object_hash($this);
}




public static function instance(DateTimeInterface $date): static
{
if ($date instanceof static) {
return clone $date;
}

$instance = parent::createFromFormat('U.u', $date->format('U.u'))
->setTimezone($date->getTimezone());

if ($date instanceof CarbonInterface) {
$settings = $date->getSettings();

if (!$date->hasLocalTranslator()) {
unset($settings['locale']);
}

$instance->settings($settings);
}

return $instance;
}










public static function rawParse(
DateTimeInterface|WeekDay|Month|string|int|float|null $time,
DateTimeZone|string|int|null $timezone = null,
): static {
if ($time instanceof DateTimeInterface) {
return static::instance($time);
}

try {
return new static($time, $timezone);
} catch (Exception $exception) {

try {
$date = @static::now($timezone)->change($time);
} catch (DateMalformedStringException|InvalidFormatException) {
$date = null;
}


return $date
?? throw new InvalidFormatException("Could not parse '$time': ".$exception->getMessage(), 0, $exception);
}
}










public static function parse(
DateTimeInterface|WeekDay|Month|string|int|float|null $time,
DateTimeZone|string|int|null $timezone = null,
): static {
$function = static::$parseFunction;

if (!$function) {
return static::rawParse($time, $timezone);
}

if (\is_string($function) && method_exists(static::class, $function)) {
$function = [static::class, $function];
}

return $function(...\func_get_args());
}











public static function parseFromLocale(
string $time,
?string $locale = null,
DateTimeZone|string|int|null $timezone = null,
): static {
return static::rawParse(static::translateTimeString($time, $locale, static::DEFAULT_LOCALE), $timezone);
}




public static function now(DateTimeZone|string|int|null $timezone = null): static
{
return new static(null, $timezone);
}




public static function today(DateTimeZone|string|int|null $timezone = null): static
{
return static::rawParse('today', $timezone);
}




public static function tomorrow(DateTimeZone|string|int|null $timezone = null): static
{
return static::rawParse('tomorrow', $timezone);
}




public static function yesterday(DateTimeZone|string|int|null $timezone = null): static
{
return static::rawParse('yesterday', $timezone);
}

private static function assertBetween($unit, $value, $min, $max): void
{
if (static::isStrictModeEnabled() && ($value < $min || $value > $max)) {
throw new OutOfRangeException($unit, $min, $max, $value);
}
}

private static function createNowInstance($timezone)
{
if (!static::hasTestNow()) {
return static::now($timezone);
}

$now = static::getTestNow();

if ($now instanceof Closure) {
return $now(static::now($timezone));
}

$now = $now->avoidMutation();

return $timezone === null ? $now : $now->setTimezone($timezone);
}

























public static function create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $timezone = null): ?self
{
$month = self::monthToInt($month);

if ((\is_string($year) && !is_numeric($year)) || $year instanceof DateTimeInterface) {
return static::parse($year, $timezone ?? (\is_string($month) || $month instanceof DateTimeZone ? $month : null));
}

$defaults = null;
$getDefault = function ($unit) use ($timezone, &$defaults) {
if ($defaults === null) {
$now = self::createNowInstance($timezone);

$defaults = array_combine([
'year',
'month',
'day',
'hour',
'minute',
'second',
], explode('-', $now->rawFormat('Y-n-j-G-i-s.u')));
}

return $defaults[$unit];
};

$year = $year ?? $getDefault('year');
$month = $month ?? $getDefault('month');
$day = $day ?? $getDefault('day');
$hour = $hour ?? $getDefault('hour');
$minute = $minute ?? $getDefault('minute');
$second = (float) ($second ?? $getDefault('second'));

self::assertBetween('month', $month, 0, 99);
self::assertBetween('day', $day, 0, 99);
self::assertBetween('hour', $hour, 0, 99);
self::assertBetween('minute', $minute, 0, 99);
self::assertBetween('second', $second, 0, 99);

$fixYear = null;

if ($year < 0) {
$fixYear = $year;
$year = 0;
} elseif ($year > 9999) {
$fixYear = $year - 9999;
$year = 9999;
}

$second = ($second < 10 ? '0' : '').number_format($second, 6);
$instance = static::rawCreateFromFormat('!Y-n-j G:i:s.u', \sprintf('%s-%s-%s %s:%02s:%02s', $year, $month, $day, $hour, $minute, $second), $timezone);

if ($instance && $fixYear !== null) {
$instance = $instance->addYears($fixYear);
}

return $instance ?? null;
}




























public static function createSafe($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $timezone = null): ?self
{
$month = self::monthToInt($month);
$fields = static::getRangesByUnit();

foreach ($fields as $field => $range) {
if ($$field !== null && (!\is_int($$field) || $$field < $range[0] || $$field > $range[1])) {
if (static::isStrictModeEnabled()) {
throw new InvalidDateException($field, $$field);
}

return null;
}
}

$instance = static::create($year, $month, $day, $hour, $minute, $second, $timezone);

foreach (array_reverse($fields) as $field => $range) {
if ($$field !== null && (!\is_int($$field) || $$field !== $instance->$field)) {
if (static::isStrictModeEnabled()) {
throw new InvalidDateException($field, $$field);
}

return null;
}
}

return $instance;
}


















public static function createStrict(?int $year = 0, ?int $month = 1, ?int $day = 1, ?int $hour = 0, ?int $minute = 0, ?int $second = 0, $timezone = null): static
{
$initialStrictMode = static::isStrictModeEnabled();
static::useStrictMode(true);

try {
$date = static::create($year, $month, $day, $hour, $minute, $second, $timezone);
} finally {
static::useStrictMode($initialStrictMode);
}

return $date;
}













public static function createFromDate($year = null, $month = null, $day = null, $timezone = null)
{
return static::create($year, $month, $day, null, null, null, $timezone);
}













public static function createMidnightDate($year = null, $month = null, $day = null, $timezone = null)
{
return static::create($year, $month, $day, 0, 0, 0, $timezone);
}













public static function createFromTime($hour = 0, $minute = 0, $second = 0, $timezone = null): static
{
return static::create(null, null, null, $hour, $minute, $second, $timezone);
}






public static function createFromTimeString(string $time, DateTimeZone|string|int|null $timezone = null): static
{
return static::today($timezone)->setTimeFromTimeString($time);
}

private static function createFromFormatAndTimezone(
string $format,
string $time,
DateTimeZone|string|int|null $originalTimezone,
): ?DateTimeInterface {
if ($originalTimezone === null) {
return parent::createFromFormat($format, $time) ?: null;
}

$timezone = \is_int($originalTimezone) ? self::getOffsetTimezone($originalTimezone) : $originalTimezone;

$timezone = static::safeCreateDateTimeZone($timezone, $originalTimezone);

return parent::createFromFormat($format, $time, $timezone) ?: null;
}

private static function getOffsetTimezone(int $offset): string
{
$minutes = (int) ($offset * static::MINUTES_PER_HOUR * static::SECONDS_PER_MINUTE);

return @timezone_name_from_abbr('', $minutes, 1) ?: throw new InvalidTimeZoneException(
"Invalid offset timezone $offset",
);
}












public static function rawCreateFromFormat(string $format, string $time, $timezone = null): ?self
{

$format = preg_replace('/(?<!\\\\)((?:\\\\{2})*)c/', '$1Y-m-d\TH:i:sP', $format);

if (preg_match('/(?<!\\\\)(?:\\\\{2})*(a|A)/', $format, $aMatches, PREG_OFFSET_CAPTURE) &&
preg_match('/(?<!\\\\)(?:\\\\{2})*(h|g|H|G)/', $format, $hMatches, PREG_OFFSET_CAPTURE) &&
$aMatches[1][1] < $hMatches[1][1] &&
preg_match('/(am|pm|AM|PM)/', $time)
) {
$format = preg_replace('/^(.*)(?<!\\\\)((?:\\\\{2})*)(a|A)(.*)$/U', '$1$2$4 $3', $format);
$time = preg_replace('/^(.*)(am|pm|AM|PM)(.*)$/U', '$1$3 $2', $time);
}

if ($timezone === false) {
$timezone = null;
}


$date = self::createFromFormatAndTimezone($format, $time, $timezone);
$lastErrors = parent::getLastErrors();

$mock = static::getMockedTestNow($timezone);

if ($mock && $date instanceof DateTimeInterface) {


$nonEscaped = '(?<!\\\\)(\\\\{2})*';

$nonIgnored = preg_replace("/^.*{$nonEscaped}!/s", '', $format);

if ($timezone === null && !preg_match("/{$nonEscaped}[eOPT]/", $nonIgnored)) {
$timezone = clone $mock->getTimezone();
}

$mock = $mock->copy();


if (!preg_match("/{$nonEscaped}[!|]/", $format)) {
if (preg_match('/[HhGgisvuB]/', $format)) {
$mock = $mock->setTime(0, 0);
}

$format = static::MOCK_DATETIME_FORMAT.' '.$format;
$time = ($mock instanceof self ? $mock->rawFormat(static::MOCK_DATETIME_FORMAT) : $mock->format(static::MOCK_DATETIME_FORMAT)).' '.$time;
}


$date = self::createFromFormatAndTimezone($format, $time, $timezone);
}

if ($date instanceof DateTimeInterface) {
$instance = static::instance($date);
$instance::setLastErrors($lastErrors);

return $instance;
}

if (static::isStrictModeEnabled()) {
throw new InvalidFormatException(implode(PHP_EOL, (array) $lastErrors['errors']));
}

return null;
}












#[ReturnTypeWillChange]
public static function createFromFormat($format, $time, $timezone = null): ?self
{
$function = static::$createFromFormatFunction;


if (\is_int($time) && \in_array(ltrim($format, '!'), ['U', 'Y', 'y', 'X', 'x', 'm', 'n', 'd', 'j', 'w', 'W', 'H', 'h', 'G', 'g', 'i', 's', 'u', 'z', 'v'], true)) {
$time = (string) $time;
}

if (!\is_string($time)) {
@trigger_error(
'createFromFormat() $time parameter will only accept string or integer for 1-letter format representing a numeric unit in the next version',
\E_USER_DEPRECATED,
);
$time = (string) $time;
}

if (!$function) {
return static::rawCreateFromFormat($format, $time, $timezone);
}

if (\is_string($function) && method_exists(static::class, $function)) {
$function = [static::class, $function];
}

return $function(...\func_get_args());
}














public static function createFromIsoFormat(
string $format,
string $time,
$timezone = null,
?string $locale = CarbonInterface::DEFAULT_LOCALE,
?TranslatorInterface $translator = null
): ?self {
$format = preg_replace_callback('/(?<!\\\\)(\\\\{2})*(LTS|LT|[Ll]{1,4})/', function ($match) use ($locale, $translator) {
[$code] = $match;

static $formats = null;

if ($formats === null) {
$translator ??= Translator::get($locale);

$formats = [
'LT' => static::getTranslationMessageWith($translator, 'formats.LT', $locale),
'LTS' => static::getTranslationMessageWith($translator, 'formats.LTS', $locale),
'L' => static::getTranslationMessageWith($translator, 'formats.L', $locale),
'LL' => static::getTranslationMessageWith($translator, 'formats.LL', $locale),
'LLL' => static::getTranslationMessageWith($translator, 'formats.LLL', $locale),
'LLLL' => static::getTranslationMessageWith($translator, 'formats.LLLL', $locale),
];
}

return $formats[$code] ?? preg_replace_callback(
'/MMMM|MM|DD|dddd/',
static fn (array $code) => mb_substr($code[0], 1),
$formats[strtoupper($code)] ?? '',
);
}, $format);

$format = preg_replace_callback('/(?<!\\\\)(\\\\{2})*('.CarbonInterface::ISO_FORMAT_REGEXP.'|[A-Za-z])/', function ($match) {
[$code] = $match;

static $replacements = null;

if ($replacements === null) {
$replacements = [
'OD' => 'd',
'OM' => 'M',
'OY' => 'Y',
'OH' => 'G',
'Oh' => 'g',
'Om' => 'i',
'Os' => 's',
'D' => 'd',
'DD' => 'd',
'Do' => 'd',
'd' => '!',
'dd' => '!',
'ddd' => 'D',
'dddd' => 'D',
'DDD' => 'z',
'DDDD' => 'z',
'DDDo' => 'z',
'e' => '!',
'E' => '!',
'H' => 'G',
'HH' => 'H',
'h' => 'g',
'hh' => 'h',
'k' => 'G',
'kk' => 'G',
'hmm' => 'gi',
'hmmss' => 'gis',
'Hmm' => 'Gi',
'Hmmss' => 'Gis',
'm' => 'i',
'mm' => 'i',
'a' => 'a',
'A' => 'a',
's' => 's',
'ss' => 's',
'S' => '*',
'SS' => '*',
'SSS' => '*',
'SSSS' => '*',
'SSSSS' => '*',
'SSSSSS' => 'u',
'SSSSSSS' => 'u*',
'SSSSSSSS' => 'u*',
'SSSSSSSSS' => 'u*',
'M' => 'm',
'MM' => 'm',
'MMM' => 'M',
'MMMM' => 'M',
'Mo' => 'm',
'Q' => '!',
'Qo' => '!',
'G' => '!',
'GG' => '!',
'GGG' => '!',
'GGGG' => '!',
'GGGGG' => '!',
'g' => '!',
'gg' => '!',
'ggg' => '!',
'gggg' => '!',
'ggggg' => '!',
'W' => '!',
'WW' => '!',
'Wo' => '!',
'w' => '!',
'ww' => '!',
'wo' => '!',
'x' => 'U???',
'X' => 'U',
'Y' => 'Y',
'YY' => 'y',
'YYYY' => 'Y',
'YYYYY' => 'Y',
'YYYYYY' => 'Y',
'z' => 'e',
'zz' => 'e',
'Z' => 'e',
'ZZ' => 'e',
];
}

$format = $replacements[$code] ?? '?';

if ($format === '!') {
throw new InvalidFormatException("Format $code not supported for creation.");
}

return $format;
}, $format);

return static::rawCreateFromFormat($format, $time, $timezone);
}













public static function createFromLocaleFormat(string $format, string $locale, string $time, $timezone = null): ?self
{
$format = preg_replace_callback(
'/(?:\\\\[a-zA-Z]|[bfkqCEJKQRV]){2,}/',
static function (array $match) use ($locale): string {
$word = str_replace('\\', '', $match[0]);
$translatedWord = static::translateTimeString($word, $locale, static::DEFAULT_LOCALE);

return $word === $translatedWord
? $match[0]
: preg_replace('/[a-zA-Z]/', '\\\\$0', $translatedWord);
},
$format
);

return static::rawCreateFromFormat($format, static::translateTimeString($time, $locale, static::DEFAULT_LOCALE), $timezone);
}













public static function createFromLocaleIsoFormat(string $format, string $locale, string $time, $timezone = null): ?self
{
$time = static::translateTimeString($time, $locale, static::DEFAULT_LOCALE, CarbonInterface::TRANSLATE_MONTHS | CarbonInterface::TRANSLATE_DAYS | CarbonInterface::TRANSLATE_MERIDIEM);

return static::createFromIsoFormat($format, $time, $timezone, $locale);
}













public static function make($var, DateTimeZone|string|null $timezone = null): ?self
{
if ($var instanceof DateTimeInterface) {
return static::instance($var);
}

$date = null;

if (\is_string($var)) {
$var = trim($var);

if (!preg_match('/^P[\dT]/', $var) &&
!preg_match('/^R\d/', $var) &&
preg_match('/[a-z\d]/i', $var)
) {
$date = static::parse($var, $timezone);
}
}

return $date;
}








private static function setLastErrors($lastErrors): void
{
if (\is_array($lastErrors) || $lastErrors === false) {
static::$lastErrors = \is_array($lastErrors) ? $lastErrors : [
'warning_count' => 0,
'warnings' => [],
'error_count' => 0,
'errors' => [],
];
}
}




public static function getLastErrors(): array|false
{
return static::$lastErrors ?? false;
}

private static function monthToInt(mixed $value, string $unit = 'month'): mixed
{
if ($value instanceof Month) {
if ($unit !== 'month') {
throw new UnitException("Month enum cannot be used to set $unit");
}

return Month::int($value);
}

return $value;
}
}
