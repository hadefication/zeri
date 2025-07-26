<?php

declare(strict_types=1);










namespace Carbon\Traits;

use Carbon\Callback;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Closure;
use DateTimeImmutable;
use DateTimeInterface;

trait IntervalStep
{





protected $step;






public function getStep(): ?Closure
{
return $this->step;
}








public function setStep(?Closure $step): void
{
$this->step = $step;
}











public function convertDate(DateTimeInterface $dateTime, bool $negated = false): CarbonInterface
{

$carbonDate = $dateTime instanceof CarbonInterface ? $dateTime : $this->resolveCarbon($dateTime);

if ($this->step) {
$carbonDate = Callback::parameter($this->step, $carbonDate->avoidMutation());

return $carbonDate->modify(($this->step)($carbonDate, $negated)->format('Y-m-d H:i:s.u e O'));
}

if ($negated) {
return $carbonDate->rawSub($this);
}

return $carbonDate->rawAdd($this);
}




private function resolveCarbon(DateTimeInterface $dateTime): Carbon|CarbonImmutable
{
if ($dateTime instanceof DateTimeImmutable) {
return CarbonImmutable::instance($dateTime);
}

return Carbon::instance($dateTime);
}
}
