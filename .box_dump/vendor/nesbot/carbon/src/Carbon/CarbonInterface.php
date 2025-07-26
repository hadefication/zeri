<?php

declare(strict_types=1);










namespace Carbon;

use BadMethodCallException;
use Carbon\Exceptions\BadComparisonUnitException;
use Carbon\Exceptions\ImmutableException;
use Carbon\Exceptions\InvalidDateException;
use Carbon\Exceptions\InvalidFormatException;
use Carbon\Exceptions\UnknownGetterException;
use Carbon\Exceptions\UnknownMethodException;
use Carbon\Exceptions\UnknownSetterException;
use Closure;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;
use ReflectionException;
use ReturnTypeWillChange;
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
interface CarbonInterface extends DateTimeInterface, JsonSerializable
{



public const NO_ZERO_DIFF = 01;
public const JUST_NOW = 02;
public const ONE_DAY_WORDS = 04;
public const TWO_DAY_WORDS = 010;
public const SEQUENTIAL_PARTS_ONLY = 020;
public const ROUND = 040;
public const FLOOR = 0100;
public const CEIL = 0200;




public const DIFF_ABSOLUTE = 1; 
public const DIFF_RELATIVE_AUTO = 0; 
public const DIFF_RELATIVE_TO_NOW = 2;
public const DIFF_RELATIVE_TO_OTHER = 3;




public const TRANSLATE_MONTHS = 1;
public const TRANSLATE_DAYS = 2;
public const TRANSLATE_UNITS = 4;
public const TRANSLATE_MERIDIEM = 8;
public const TRANSLATE_DIFF = 0x10;
public const TRANSLATE_ALL = self::TRANSLATE_MONTHS | self::TRANSLATE_DAYS | self::TRANSLATE_UNITS | self::TRANSLATE_MERIDIEM | self::TRANSLATE_DIFF;




public const SUNDAY = 0;
public const MONDAY = 1;
public const TUESDAY = 2;
public const WEDNESDAY = 3;
public const THURSDAY = 4;
public const FRIDAY = 5;
public const SATURDAY = 6;






public const JANUARY = 1;
public const FEBRUARY = 2;
public const MARCH = 3;
public const APRIL = 4;
public const MAY = 5;
public const JUNE = 6;
public const JULY = 7;
public const AUGUST = 8;
public const SEPTEMBER = 9;
public const OCTOBER = 10;
public const NOVEMBER = 11;
public const DECEMBER = 12;




public const YEARS_PER_MILLENNIUM = 1_000;
public const YEARS_PER_CENTURY = 100;
public const YEARS_PER_DECADE = 10;
public const MONTHS_PER_YEAR = 12;
public const MONTHS_PER_QUARTER = 3;
public const QUARTERS_PER_YEAR = 4;
public const WEEKS_PER_YEAR = 52;
public const WEEKS_PER_MONTH = 4;
public const DAYS_PER_YEAR = 365;
public const DAYS_PER_WEEK = 7;
public const HOURS_PER_DAY = 24;
public const MINUTES_PER_HOUR = 60;
public const SECONDS_PER_MINUTE = 60;
public const MILLISECONDS_PER_SECOND = 1_000;
public const MICROSECONDS_PER_MILLISECOND = 1_000;
public const MICROSECONDS_PER_SECOND = 1_000_000;




public const WEEK_DAY_AUTO = 'auto';






public const RFC7231_FORMAT = 'D, d M Y H:i:s \G\M\T';






public const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';






public const MOCK_DATETIME_FORMAT = 'Y-m-d H:i:s.u';






public const ISO_FORMAT_REGEXP = '(O[YMDHhms]|[Hh]mm(ss)?|Mo|MM?M?M?|Do|DDDo|DD?D?D?|ddd?d?|do?|w[o|w]?|W[o|W]?|Qo?|YYYYYY|YYYYY|YYYY|YY?|g{1,5}|G{1,5}|e|E|a|A|hh?|HH?|kk?|mm?|ss?|S{1,9}|x|X|zz?|ZZ?)';






public const DEFAULT_LOCALE = 'en';











public function __call(string $method, array $parameters): mixed;









public static function __callStatic(string $method, array $parameters): mixed;




public function __clone(): void;









public function __construct(DateTimeInterface|WeekDay|Month|string|int|float|null $time = null, DateTimeZone|string|int|null $timezone = null);




public function __debugInfo(): array;








public function __get(string $name): mixed;








public function __isset($name);











public function __set($name, $value);








#[ReturnTypeWillChange]
public static function __set_state($dump): static;








public function __sleep();









public function __toString();














#[ReturnTypeWillChange]
public function add($unit, $value = 1, ?bool $overflow = null): static;












public function addRealUnit(string $unit, $value = 1): static;










public function addUTCUnit(string $unit, $value = 1): static;




public function addUnit(Unit|string $unit, $value = 1, ?bool $overflow = null): static;








public function addUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static;





























public function ago($syntax = null, $short = false, $parts = 1, $options = null);









public function average($date = null);









public function avoidMutation(): static;


















public function between(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool;











public function betweenExcluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool;











public function betweenIncluded(DateTimeInterface|string $date1, DateTimeInterface|string $date2): bool;












public function calendar($referenceTime = null, array $formats = []);











public static function canBeCreatedFromFormat(?string $date, string $format): bool;









public function carbonize($date = null);

/**
@template






*/
public function cast(string $className): mixed;




public function ceil(DateInterval|string|int|float $precision = 1): static;




public function ceilUnit(string $unit, DateInterval|string|int|float $precision = 1): static;






public function ceilWeek(WeekDay|int|null $weekStartsAt = null): static;















public function change($modifier);








public function cleanupDumpProperties();

/**
@alias




*/
public function clone();









public function closest($date1, $date2);






public function copy();

























public static function create($year = 0, $month = 1, $day = 1, $hour = 0, $minute = 0, $second = 0, $timezone = null);













public static function createFromDate($year = null, $month = null, $day = null, $timezone = null);












#[ReturnTypeWillChange]
public static function createFromFormat($format, $time, $timezone = null);














public static function createFromIsoFormat(string $format, string $time, $timezone = null, ?string $locale = 'en', ?TranslatorInterface $translator = null);













public static function createFromLocaleFormat(string $format, string $locale, string $time, $timezone = null);













public static function createFromLocaleIsoFormat(string $format, string $locale, string $time, $timezone = null);













public static function createFromTime($hour = 0, $minute = 0, $second = 0, $timezone = null): static;






public static function createFromTimeString(string $time, DateTimeZone|string|int|null $timezone = null): static;






public static function createFromTimestamp(string|int|float $timestamp, DateTimeZone|string|int|null $timezone = null): static;






public static function createFromTimestampMs(string|int|float $timestamp, DateTimeZone|string|int|null $timezone = null): static;










public static function createFromTimestampMsUTC($timestamp): static;






public static function createFromTimestampUTC(string|int|float $timestamp): static;













public static function createMidnightDate($year = null, $month = null, $day = null, $timezone = null);




























public static function createSafe($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null, $timezone = null);


















public static function createStrict(?int $year = 0, ?int $month = 1, ?int $day = 1, ?int $hour = 0, ?int $minute = 0, ?int $second = 0, $timezone = null): static;

/**
@template
@psalm-param
@psalm-return(T is int ? static : int)








*/
public function dayOfYear(?int $value = null): static|int;











public function diffAsCarbonInterval($date = null, bool $absolute = false, array $skip = []): CarbonInterval;











public function diffAsDateInterval($date = null, bool $absolute = false): DateInterval;











public function diffFiltered(CarbonInterval $ci, Closure $callback, $date = null, bool $absolute = false): int;



















































public function diffForHumans($other = null, $syntax = null, $short = false, $parts = 1, $options = null): string;










public function diffInDays($date = null, bool $absolute = false, bool $utc = false): float;










public function diffInDaysFiltered(Closure $callback, $date = null, bool $absolute = false): int;









public function diffInHours($date = null, bool $absolute = false): float;










public function diffInHoursFiltered(Closure $callback, $date = null, bool $absolute = false): int;









public function diffInMicroseconds($date = null, bool $absolute = false): float;









public function diffInMilliseconds($date = null, bool $absolute = false): float;









public function diffInMinutes($date = null, bool $absolute = false): float;










public function diffInMonths($date = null, bool $absolute = false, bool $utc = false): float;










public function diffInQuarters($date = null, bool $absolute = false, bool $utc = false): float;









public function diffInSeconds($date = null, bool $absolute = false): float;











public function diffInUnit(Unit|string $unit, $date = null, bool $absolute = false, bool $utc = false): float;









public function diffInWeekdays($date = null, bool $absolute = false): int;









public function diffInWeekendDays($date = null, bool $absolute = false): int;










public function diffInWeeks($date = null, bool $absolute = false, bool $utc = false): float;










public function diffInYears($date = null, bool $absolute = false, bool $utc = false): float;






public static function disableHumanDiffOption(int $humanDiffOption): void;






public static function enableHumanDiffOption(int $humanDiffOption): void;











public function endOf(Unit|string $unit, mixed ...$params): static;











public function endOfCentury();











public function endOfDay();











public function endOfDecade();









public function endOfHour(): static;











public function endOfMillennium();











public function endOfMillisecond(): static;









public function endOfMinute(): static;











public function endOfMonth();











public function endOfQuarter();











public function endOfSecond(): static;















public function endOfWeek(WeekDay|int|null $weekEndsAt = null): static;











public function endOfYear();













public function eq(DateTimeInterface|string $date): bool;











public function equalTo(DateTimeInterface|string $date): bool;










public static function executeWithLocale(string $locale, callable $func): mixed;









public function farthest($date1, $date2);











public function firstOfMonth($dayOfWeek = null);











public function firstOfQuarter($dayOfWeek = null);











public function firstOfYear($dayOfWeek = null);




public function floor(DateInterval|string|int|float $precision = 1): static;




public function floorUnit(string $unit, DateInterval|string|int|float $precision = 1): static;






public function floorWeek(WeekDay|int|null $weekStartsAt = null): static;

/**
@alias































*/
public function from($other = null, $syntax = null, $short = false, $parts = 1, $options = null);





























public function fromNow($syntax = null, $short = false, $parts = 1, $options = null);










public static function fromSerialized($value): static;









public static function genericMacro(callable $macro, int $priority = 0): void;








public function get(Unit|string $name): mixed;






public function getAltNumber(string $key): string;







public static function getAvailableLocales();







public static function getAvailableLocalesInfo();






public function getCalendarFormats(?string $locale = null): array;

public function getClock(): ?WrapperClock;




public static function getDays(): array;









public function getDaysFromStartOfWeek(WeekDay|int|null $weekStartsAt = null): int;






public static function getFallbackLocale(): ?string;




public static function getFormatsToIsoReplacements(): array;




public static function getHumanDiffOptions(): int;






public function getIsoFormats(?string $locale = null): array;




public static function getIsoUnits(): array;




public static function getLastErrors(): array|false;




public function getLocalMacro(string $name): ?callable;




public function getLocalTranslator(): TranslatorInterface;






public static function getLocale(): string;




public static function getMacro(string $name): ?callable;






public static function getMidDayAt();









public function getOffsetString(string $separator = ':'): string;









public function getPaddedUnit($unit, $length = 2, $padString = '0', $padType = 0): string;



















public function getPreciseTimestamp($precision = 6): float;




public function getSettings(): array;







public static function getTestNow(): Closure|self|null;






public static function getTimeFormatByPrecision(string $unitPrecision): string;






public function getTimestampMs(): int;








public function getTranslatedDayName(?string $context = null, string $keySuffix = '', ?string $defaultValue = null): string;






public function getTranslatedMinDayName(?string $context = null): string;








public function getTranslatedMonthName(?string $context = null, string $keySuffix = '', ?string $defaultValue = null): string;






public function getTranslatedShortDayName(?string $context = null): string;






public function getTranslatedShortMonthName(?string $context = null): string;











public function getTranslationMessage(string $key, ?string $locale = null, ?string $default = null, $translator = null);











public static function getTranslationMessageWith($translator, string $key, ?string $locale = null, ?string $default = null);




public static function getTranslator(): TranslatorInterface;








public static function getWeekEndsAt(?string $locale = null): int;






public static function getWeekStartsAt(?string $locale = null): int;




public static function getWeekendDays(): array;











public function greaterThan(DateTimeInterface|string $date): bool;











public function greaterThanOrEqualTo(DateTimeInterface|string $date): bool;













public function gt(DateTimeInterface|string $date): bool;













public function gte(DateTimeInterface|string $date): bool;










public static function hasFormat(string $date, string $format): bool;















public static function hasFormatWithModifiers(?string $date, string $format): bool;




public function hasLocalMacro(string $name): bool;




public function hasLocalTranslator(): bool;








public static function hasMacro(string $name): bool;






public static function hasRelativeKeywords(?string $time): bool;







public static function hasTestNow(): bool;




public static function instance(DateTimeInterface $date): static;























public function is(WeekDay|Month|string $tester): bool;













public function isAfter(DateTimeInterface|string $date): bool;













public function isBefore(DateTimeInterface|string $date): bool;














public function isBetween(DateTimeInterface|string $date1, DateTimeInterface|string $date2, bool $equal = true): bool;
















public function isBirthday(DateTimeInterface|string|null $date = null): bool;














public function isCurrentUnit(string $unit): bool;
















public function isDayOfWeek($dayOfWeek): bool;




public function isEndOfCentury(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;





















public function isEndOfDay(Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false, Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfDecade(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfHour(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfMillennium(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfMillisecond(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfMinute(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfMonth(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfQuarter(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isEndOfSecond(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;






public function isEndOfTime(): bool;










public function isEndOfUnit(Unit $unit, Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null, mixed ...$params): bool;










public function isEndOfWeek(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null, WeekDay|int|null $weekEndsAt = null): bool;




public function isEndOfYear(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;










public function isFuture(): bool;




public static function isImmutable(): bool;













public function isLastOfMonth(): bool;










public function isLeapYear(): bool;















public function isLongIsoYear(): bool;

















public function isLongYear(): bool;












public function isMidday(): bool;











public function isMidnight(): bool;








public static function isModifiableUnit($unit): bool;




public static function isMutable(): bool;











public function isNowOrFuture(): bool;











public function isNowOrPast(): bool;










public function isPast(): bool;













public function isSameAs(string $format, DateTimeInterface|string $date): bool;

















public function isSameMonth(DateTimeInterface|string $date, bool $ofSameYear = true): bool;

















public function isSameQuarter(DateTimeInterface|string $date, bool $ofSameYear = true): bool;

















public function isSameUnit(string $unit, DateTimeInterface|string $date): bool;




public function isStartOfCentury(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;



















public function isStartOfDay(Unit|DateInterval|Closure|CarbonConverterInterface|string|bool $checkMicroseconds = false, Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfDecade(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfHour(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfMillennium(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfMillisecond(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfMinute(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfMonth(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfQuarter(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;




public function isStartOfSecond(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;






public function isStartOfTime(): bool;










public function isStartOfUnit(Unit $unit, Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null, mixed ...$params): bool;










public function isStartOfWeek(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null, WeekDay|int|null $weekStartsAt = null): bool;




public function isStartOfYear(Unit|DateInterval|Closure|CarbonConverterInterface|string|null $interval = null): bool;







public static function isStrictModeEnabled(): bool;










public function isToday(): bool;










public function isTomorrow(): bool;










public function isWeekday(): bool;










public function isWeekend(): bool;










public function isYesterday(): bool;






public function isoFormat(string $format, ?string $originalFormat = null): string;












public function isoWeek($week = null, $dayOfWeek = null, $dayOfYear = null);












public function isoWeekYear($year = null, $dayOfWeek = null, $dayOfYear = null);






public function isoWeekday(WeekDay|int|null $value = null): static|int;











public function isoWeeksInYear($dayOfWeek = null, $dayOfYear = null);




public function jsonSerialize(): mixed;











public function lastOfMonth($dayOfWeek = null);











public function lastOfQuarter($dayOfWeek = null);











public function lastOfYear($dayOfWeek = null);











public function lessThan(DateTimeInterface|string $date): bool;











public function lessThanOrEqualTo(DateTimeInterface|string $date): bool;









public function locale(?string $locale = null, string ...$fallbackLocales): static|string;









public static function localeHasDiffOneDayWords(string $locale): bool;









public static function localeHasDiffSyntax(string $locale): bool;









public static function localeHasDiffTwoDayWords(string $locale): bool;









public static function localeHasPeriodSyntax($locale);









public static function localeHasShortUnits(string $locale): bool;













public function lt(DateTimeInterface|string $date): bool;













public function lte(DateTimeInterface|string $date): bool;

/**
@param-closure-this
















*/
public static function macro(string $name, ?callable $macro): void;













public static function make($var, DateTimeZone|string|null $timezone = null);








public function max($date = null);










public function maximum($date = null);






public function meridiem(bool $isLower = false): string;






public function midDay();








public function min($date = null);










public function minimum($date = null);




























public static function mixin(object|string $mixin): void;








#[ReturnTypeWillChange]
public function modify($modify);













public function ne(DateTimeInterface|string $date): bool;











public function next($modifier = null);






public function nextWeekday();






public function nextWeekendDay();











public function notEqualTo(DateTimeInterface|string $date): bool;




public static function now(DateTimeZone|string|int|null $timezone = null): static;






public function nowWithSameTz(): static;












public function nthOfMonth($nth, $dayOfWeek);












public function nthOfQuarter($nth, $dayOfWeek);












public function nthOfYear($nth, $dayOfWeek);




public function ordinal(string $key, ?string $period = null): string;










public static function parse(DateTimeInterface|WeekDay|Month|string|int|float|null $time, DateTimeZone|string|int|null $timezone = null): static;











public static function parseFromLocale(string $time, ?string $locale = null, DateTimeZone|string|int|null $timezone = null): static;




public static function pluralUnit(string $unit): string;











public function previous($modifier = null);






public function previousWeekday();






public function previousWeekendDay();








public function range($end = null, $interval = null, $unit = null): CarbonPeriod;








public function rawAdd(DateInterval $interval): static;












public static function rawCreateFromFormat(string $format, string $time, $timezone = null);




public function rawFormat(string $format): string;










public static function rawParse(DateTimeInterface|WeekDay|Month|string|int|float|null $time, DateTimeZone|string|int|null $timezone = null): static;




public function rawSub(DateInterval $interval): static;




public static function resetMacros(): void;












public static function resetMonthsOverflow(): void;






public static function resetToStringFormat(): void;












public static function resetYearsOverflow(): void;




public function round(DateInterval|string|int|float $precision = 1, callable|string $function = 'round'): static;




public function roundUnit(string $unit, DateInterval|string|int|float $precision = 1, callable|string $function = 'round'): static;






public function roundWeek(WeekDay|int|null $weekStartsAt = null): static;






public function secondsSinceMidnight(): float;






public function secondsUntilEndOfDay(): float;




public function serialize(): string;







public static function serializeUsing(callable|string|null $format): void;








public function set(Unit|array|string $name, DateTimeZone|Month|string|int|float|null $value = null): static;






public function setDate(int $year, int $month, int $day): static;




public function setDateFrom(DateTimeInterface|string $date): static;




public function setDateTime(int $year, int $month, int $day, int $hour, int $minute, int $second = 0, int $microseconds = 0): static;




public function setDateTimeFrom(DateTimeInterface|string $date): static;










public function setDaysFromStartOfWeek(int $numberOfDays, WeekDay|int|null $weekStartsAt = null): static;








public static function setFallbackLocale(string $locale): void;






public static function setHumanDiffOptions(int $humanDiffOptions): void;






public function setISODate(int $year, int $week, int $day = 1): static;




public function setLocalTranslator(TranslatorInterface $translator);







public static function setLocale(string $locale): void;















public static function setMidDayAt($hour);























public static function setTestNow(mixed $testNow = null): void;




















public static function setTestNowAndTimezone($testNow = null, $timezone = null): void;






public function setTime(int $hour, int $minute, int $second = 0, int $microseconds = 0): static;




public function setTimeFrom(DateTimeInterface|string $date): static;




public function setTimeFromTimeString(string $time): static;






public function setTimestamp(string|int|float $timestamp): static;




public function setTimezone(DateTimeZone|string|int $timeZone): static;













public static function setToStringFormat(Closure|string|null $format): void;








public static function setTranslator(TranslatorInterface $translator): void;







public function setUnit(string $unit, Month|int|float|null $value = null): static;








public function setUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static;
























public static function setWeekendDays(array $days): void;


















public function settings(array $settings): static;




public function shiftTimezone(DateTimeZone|string $value): static;






public static function shouldOverflowMonths(): bool;






public static function shouldOverflowYears(): bool;

/**
@alias



*/
public function since($other = null, $syntax = null, $short = false, $parts = 1, $options = null);




public static function singularUnit(string $unit): string;

public static function sleep(int|float $seconds): void;











public function startOf(Unit|string $unit, mixed ...$params): static;











public function startOfCentury();











public function startOfDay();











public function startOfDecade();









public function startOfHour(): static;











public function startOfMillennium();











public function startOfMillisecond(): static;









public function startOfMinute(): static;











public function startOfMonth();











public function startOfQuarter();











public function startOfSecond(): static;















public function startOfWeek(WeekDay|int|null $weekStartsAt = null): static;











public function startOfYear();














#[ReturnTypeWillChange]
public function sub($unit, $value = 1, ?bool $overflow = null): static;












public function subRealUnit($unit, $value = 1): static;










public function subUTCUnit($unit, $value = 1): static;




public function subUnit(Unit|string $unit, $value = 1, ?bool $overflow = null): static;








public function subUnitNoOverflow(string $valueUnit, int $value, string $overflowUnit): static;












public function subtract($unit, $value = 1, ?bool $overflow = null): static;







public function timespan($other = null, $timezone = null): string;






public function timestamp(string|int|float $timestamp): static;

/**
@alias
*/
public function timezone(DateTimeZone|string|int $value): static;

















































public function to($other = null, $syntax = null, $short = false, $parts = 1, $options = null);









public function toArray(): array;









public function toAtomString(): string;









public function toCookieString(): string;

/**
@alias







*/
public function toDate(): DateTime;









public function toDateString(): string;









public function toDateTime(): DateTime;









public function toDateTimeImmutable(): DateTimeImmutable;











public function toDateTimeLocalString(string $unitPrecision = 'second'): string;









public function toDateTimeString(string $unitPrecision = 'second'): string;









public function toDayDateTimeString(): string;









public function toFormattedDateString(): string;









public function toFormattedDayDateString(): string;













public function toISOString(bool $keepOffset = false): ?string;






public function toImmutable();









public function toIso8601String(): string;









public function toIso8601ZuluString(string $unitPrecision = 'second'): string;









public function toJSON(): ?string;






public function toMutable();





























public function toNow($syntax = null, $short = false, $parts = 1, $options = null);









public function toObject(): object;








public function toPeriod($end = null, $interval = null, $unit = null): CarbonPeriod;









public function toRfc1036String(): string;









public function toRfc1123String(): string;









public function toRfc2822String(): string;










public function toRfc3339String(bool $extended = false): string;









public function toRfc7231String(): string;









public function toRfc822String(): string;









public function toRfc850String(): string;









public function toRssString(): string;









public function toString(): string;









public function toTimeString(string $unitPrecision = 'second'): string;









public function toW3cString(): string;




public static function today(DateTimeZone|string|int|null $timezone = null): static;




public static function tomorrow(DateTimeZone|string|int|null $timezone = null): static;












public function translate(string $key, array $parameters = [], string|int|float|null $number = null, ?TranslatorInterface $translator = null, bool $altNumbers = false): string;








public function translateNumber(int $number): string;

















public static function translateTimeString(string $timeString, ?string $from = null, ?string $to = null, int $mode = self::TRANSLATE_ALL): string;









public function translateTimeStringTo(string $timeString, ?string $to = null): string;











public static function translateWith(TranslatorInterface $translator, string $key, array $parameters = [], $number = null): string;





public function translatedFormat(string $format): string;




public function tz(DateTimeZone|string|int|null $value = null): static|string;

/**
@alias




*/
public function unix(): int;

/**
@alias































*/
public function until($other = null, $syntax = null, $short = false, $parts = 1, $options = null);














public static function useMonthsOverflow(bool $monthsOverflow = true): void;










public static function useStrictMode(bool $strictModeEnabled = true): void;














public static function useYearsOverflow(bool $yearsOverflow = true): void;




public function utc(): static;




public function utcOffset(?int $minuteOffset = null): static|int;






public function valueOf(): float;












public function week($week = null, $dayOfWeek = null, $dayOfYear = null);












public function weekYear($year = null, $dayOfWeek = null, $dayOfYear = null);






public function weekday(WeekDay|int|null $value = null): static|int;











public function weeksInYear($dayOfWeek = null, $dayOfYear = null);

/**
@template











*/
public static function withTestNow(mixed $testNow, callable $callback): mixed;




public static function yesterday(DateTimeZone|string|int|null $timezone = null): static;


}
