<?php

declare(strict_types=1);










namespace Carbon;

use Carbon\Exceptions\InvalidFormatException;

enum WeekDay: int
{

case Sunday = 0; 
case Monday = 1; 
case Tuesday = 2; 
case Wednesday = 3; 
case Thursday = 4; 
case Friday = 5; 
case Saturday = 6; 

public static function int(self|int|null $value): ?int
{
return $value instanceof self ? $value->value : $value;
}

public static function fromNumber(int $number): self
{
$day = $number % CarbonInterface::DAYS_PER_WEEK;

return self::from($day + ($day < 0 ? CarbonInterface::DAYS_PER_WEEK : 0));
}

public static function fromName(string $name, ?string $locale = null): self
{
try {
return self::from(CarbonImmutable::parseFromLocale($name, $locale)->dayOfWeek);
} catch (InvalidFormatException $exception) {

if ($locale !== null && !mb_strlen($name) < 4 && !str_ends_with($name, '.')) {
try {
return self::from(CarbonImmutable::parseFromLocale($name.'.', $locale)->dayOfWeek);
} catch (InvalidFormatException) {

}
}

throw $exception;
}
}

public function next(?CarbonImmutable $now = null): CarbonImmutable
{
return $now?->modify($this->name) ?? new CarbonImmutable($this->name);
}

public function locale(string $locale, ?CarbonImmutable $now = null): CarbonImmutable
{
return $this->next($now)->locale($locale);
}
}
