<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Exceptions\InvalidCastException;
use Carbon\Exceptions\InvalidTimeZoneException;
use Carbon\Traits\LocalFactory;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use Throwable;

class CarbonTimeZone extends DateTimeZone
{
use LocalFactory;

public const MAXIMUM_TIMEZONE_OFFSET = 99;

public function __construct(string|int|float $timezone)
{
$this->initLocalFactory();

parent::__construct(static::getDateTimeZoneNameFromMixed($timezone));
}

protected static function parseNumericTimezone(string|int|float $timezone): string
{
if (abs((float) $timezone) > static::MAXIMUM_TIMEZONE_OFFSET) {
throw new InvalidTimeZoneException(
'Absolute timezone offset cannot be greater than '.
static::MAXIMUM_TIMEZONE_OFFSET.'.',
);
}

return ($timezone >= 0 ? '+' : '').ltrim((string) $timezone, '+').':00';
}

protected static function getDateTimeZoneNameFromMixed(string|int|float $timezone): string
{
if (\is_string($timezone)) {
$timezone = preg_replace('/^\s*([+-]\d+)(\d{2})\s*$/', '$1:$2', $timezone);
}

if (is_numeric($timezone)) {
return static::parseNumericTimezone($timezone);
}

return $timezone;
}








public function cast(string $className): mixed
{
if (!method_exists($className, 'instance')) {
if (is_a($className, DateTimeZone::class, true)) {
return new $className($this->getName());
}

throw new InvalidCastException("$className has not the instance() method needed to cast the date.");
}

return $className::instance($this);
}











public static function instance(
DateTimeZone|string|int|false|null $object,
DateTimeZone|string|int|false|null $objectDump = null,
): ?self {
$timezone = $object;

if ($timezone instanceof static) {
return $timezone;
}

if ($timezone === null || $timezone === false) {
return null;
}

try {
if (!($timezone instanceof DateTimeZone)) {
$name = static::getDateTimeZoneNameFromMixed($object);
$timezone = new static($name);
}

return $timezone instanceof static ? $timezone : new static($timezone->getName());
} catch (Exception $exception) {
throw new InvalidTimeZoneException(
'Unknown or bad timezone ('.($objectDump ?: $object).')',
previous: $exception,
);
}
}








public function getAbbreviatedName(bool $dst = false): string
{
$name = $this->getName();

$date = new DateTimeImmutable($dst ? 'July 1' : 'January 1', $this);
$timezone = $date->format('T');
$abbreviations = $this->listAbbreviations();
$matchingZones = array_merge($abbreviations[$timezone] ?? [], $abbreviations[strtolower($timezone)] ?? []);

if ($matchingZones !== []) {
foreach ($matchingZones as $zone) {
if ($zone['timezone_id'] === $name && $zone['dst'] == $dst) {
return $timezone;
}
}
}

foreach ($abbreviations as $abbreviation => $zones) {
foreach ($zones as $zone) {
if ($zone['timezone_id'] === $name && $zone['dst'] == $dst) {
return strtoupper($abbreviation);
}
}
}

return 'unknown';
}

/**
@alias






*/
public function getAbbr(bool $dst = false): string
{
return $this->getAbbreviatedName($dst);
}




public function toOffsetName(?DateTimeInterface $date = null): string
{
return static::getOffsetNameFromMinuteOffset(
$this->getOffset($this->resolveCarbon($date)) / 60,
);
}




public function toOffsetTimeZone(?DateTimeInterface $date = null): static
{
return new static($this->toOffsetName($date));
}







public function toRegionName(?DateTimeInterface $date = null, int $isDST = 1): ?string
{
$name = $this->getName();
$firstChar = substr($name, 0, 1);

if ($firstChar !== '+' && $firstChar !== '-') {
return $name;
}

$date = $this->resolveCarbon($date);



try {
$offset = @$this->getOffset($date) ?: 0;
} catch (Throwable) {
$offset = 0;
}


$name = @timezone_name_from_abbr('', $offset, $isDST);

if ($name) {
return $name;
}

foreach (timezone_identifiers_list() as $timezone) {
if (Carbon::instance($date)->setTimezone($timezone)->getOffset() === $offset) {
return $timezone;
}
}

return null;
}




public function toRegionTimeZone(?DateTimeInterface $date = null): ?self
{
$timezone = $this->toRegionName($date);

if ($timezone !== null) {
return new static($timezone);
}

if (Carbon::isStrictModeEnabled()) {
throw new InvalidTimeZoneException('Unknown timezone for offset '.$this->getOffset($this->resolveCarbon($date)).' seconds.');
}

return null;
}






public function __toString()
{
return $this->getName();
}








public function getType(): int
{
return preg_match('/"timezone_type";i:(\d)/', serialize($this), $match) ? (int) $match[1] : 3;
}








public static function create($object = null)
{
return static::instance($object);
}








public static function createFromHourOffset(float $hourOffset)
{
return static::createFromMinuteOffset($hourOffset * Carbon::MINUTES_PER_HOUR);
}








public static function createFromMinuteOffset(float $minuteOffset)
{
return static::instance(static::getOffsetNameFromMinuteOffset($minuteOffset));
}








public static function getOffsetNameFromMinuteOffset(float $minutes): string
{
$minutes = round($minutes);
$unsignedMinutes = abs($minutes);

return ($minutes < 0 ? '-' : '+').
str_pad((string) floor($unsignedMinutes / 60), 2, '0', STR_PAD_LEFT).
':'.
str_pad((string) ($unsignedMinutes % 60), 2, '0', STR_PAD_LEFT);
}

private function resolveCarbon(?DateTimeInterface $date): DateTimeInterface
{
if ($date) {
return $date;
}

if (isset($this->clock)) {
return $this->clock->now()->setTimezone($this);
}

return Carbon::now($this);
}
}
