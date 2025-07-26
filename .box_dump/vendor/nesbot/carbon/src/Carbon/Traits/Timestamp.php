<?php

declare(strict_types=1);










namespace Carbon\Traits;

use DateTimeZone;




trait Timestamp
{





#[\ReturnTypeWillChange]
public static function createFromTimestamp(
float|int|string $timestamp,
DateTimeZone|string|int|null $timezone = null,
): static {
$date = static::createFromTimestampUTC($timestamp);

return $timezone === null ? $date : $date->setTimezone($timezone);
}






public static function createFromTimestampUTC(float|int|string $timestamp): static
{
[$integer, $decimal] = self::getIntegerAndDecimalParts($timestamp);
$delta = floor($decimal / static::MICROSECONDS_PER_SECOND);
$integer += $delta;
$decimal -= $delta * static::MICROSECONDS_PER_SECOND;
$decimal = str_pad((string) $decimal, 6, '0', STR_PAD_LEFT);

return static::rawCreateFromFormat('U u', "$integer $decimal");
}










public static function createFromTimestampMsUTC($timestamp): static
{
[$milliseconds, $microseconds] = self::getIntegerAndDecimalParts($timestamp, 3);
$sign = $milliseconds < 0 || ($milliseconds === 0.0 && $microseconds < 0) ? -1 : 1;
$milliseconds = abs($milliseconds);
$microseconds = $sign * abs($microseconds) + static::MICROSECONDS_PER_MILLISECOND * ($milliseconds % static::MILLISECONDS_PER_SECOND);
$seconds = $sign * floor($milliseconds / static::MILLISECONDS_PER_SECOND);
$delta = floor($microseconds / static::MICROSECONDS_PER_SECOND);
$seconds = (int) ($seconds + $delta);
$microseconds -= $delta * static::MICROSECONDS_PER_SECOND;
$microseconds = str_pad((string) (int) $microseconds, 6, '0', STR_PAD_LEFT);

return static::rawCreateFromFormat('U u', "$seconds $microseconds");
}






public static function createFromTimestampMs(
float|int|string $timestamp,
DateTimeZone|string|int|null $timezone = null,
): static {
$date = static::createFromTimestampMsUTC($timestamp);

return $timezone === null ? $date : $date->setTimezone($timezone);
}






public function timestamp(float|int|string $timestamp): static
{
return $this->setTimestamp($timestamp);
}



















public function getPreciseTimestamp($precision = 6): float
{
return round(((float) $this->rawFormat('Uu')) / pow(10, 6 - $precision));
}






public function valueOf(): float
{
return $this->getPreciseTimestamp(3);
}






public function getTimestampMs(): int
{
return (int) $this->getPreciseTimestamp(3);
}

/**
@alias




*/
public function unix(): int
{
return $this->getTimestamp();
}













private static function getIntegerAndDecimalParts($numbers, $decimals = 6): array
{
if (\is_int($numbers) || \is_float($numbers)) {
$numbers = number_format($numbers, $decimals, '.', '');
}

$sign = str_starts_with($numbers, '-') ? -1 : 1;
$integer = 0;
$decimal = 0;

foreach (preg_split('`[^\d.]+`', $numbers) as $chunk) {
[$integerPart, $decimalPart] = explode('.', "$chunk.");

$integer += (int) $integerPart;
$decimal += (float) ("0.$decimalPart");
}

$overflow = floor($decimal);
$integer += $overflow;
$decimal -= $overflow;

return [$sign * $integer, $decimal === 0.0 ? 0.0 : $sign * round($decimal * pow(10, $decimals))];
}
}
