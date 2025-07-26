<?php

declare(strict_types=1);










namespace Carbon;

class CarbonPeriodImmutable extends CarbonPeriod
{





protected const DEFAULT_DATE_CLASS = CarbonImmutable::class;




protected string $dateClass = CarbonImmutable::class;





protected function copyIfImmutable(): static
{
return $this->constructed ? clone $this : $this;
}
}
