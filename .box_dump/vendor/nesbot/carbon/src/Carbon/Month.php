<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Exceptions\InvalidFormatException;

enum Month: int
{

case January = 1; 
case February = 2; 
case March = 3; 
case April = 4; 
case May = 5; 
case June = 6; 
case July = 7; 
case August = 8; 
case September = 9; 
case October = 10; 
case November = 11; 
case December = 12; 

public static function int(self|int|null $value): ?int
{
return $value instanceof self ? $value->value : $value;
}

public static function fromNumber(int $number): self
{
$month = $number % CarbonInterface::MONTHS_PER_YEAR;

return self::from($month + ($month < 1 ? CarbonInterface::MONTHS_PER_YEAR : 0));
}

public static function fromName(string $name, ?string $locale = null): self
{
try {
return self::from(CarbonImmutable::parseFromLocale("$name 1", $locale)->month);
} catch (InvalidFormatException $exception) {

if ($locale !== null && !mb_strlen($name) < 4 && !str_ends_with($name, '.')) {
try {
return self::from(CarbonImmutable::parseFromLocale("$name. 1", $locale)->month);
} catch (InvalidFormatException $e) {

}
}

throw $exception;
}
}

public function ofTheYear(CarbonImmutable|int|null $now = null): CarbonImmutable
{
if (\is_int($now)) {
return CarbonImmutable::create($now, $this->value);
}

$modifier = $this->name.' 1st';

return $now?->modify($modifier) ?? new CarbonImmutable($modifier);
}

public function locale(string $locale, ?CarbonImmutable $now = null): CarbonImmutable
{
return $this->ofTheYear($now)->locale($locale);
}
}
