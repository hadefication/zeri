<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

use function Illuminate\Support\enum_value;

trait ManagesFrequencies
{






public function cron($expression)
{
$this->expression = $expression;

return $this;
}








public function between($startTime, $endTime)
{
return $this->when($this->inTimeInterval($startTime, $endTime));
}








public function unlessBetween($startTime, $endTime)
{
return $this->skip($this->inTimeInterval($startTime, $endTime));
}








private function inTimeInterval($startTime, $endTime)
{
[$now, $startTime, $endTime] = [
Carbon::now($this->timezone),
Carbon::parse($startTime, $this->timezone),
Carbon::parse($endTime, $this->timezone),
];

if ($endTime->lessThan($startTime)) {
if ($startTime->greaterThan($now)) {
$startTime = $startTime->subDay(1);
} else {
$endTime = $endTime->addDay(1);
}
}

return fn () => $now->between($startTime, $endTime);
}






public function everySecond()
{
return $this->repeatEvery(1);
}






public function everyTwoSeconds()
{
return $this->repeatEvery(2);
}






public function everyFiveSeconds()
{
return $this->repeatEvery(5);
}






public function everyTenSeconds()
{
return $this->repeatEvery(10);
}






public function everyFifteenSeconds()
{
return $this->repeatEvery(15);
}






public function everyTwentySeconds()
{
return $this->repeatEvery(20);
}






public function everyThirtySeconds()
{
return $this->repeatEvery(30);
}







protected function repeatEvery($seconds)
{
if (60 % $seconds !== 0) {
throw new InvalidArgumentException("The seconds [$seconds] are not evenly divisible by 60.");
}

$this->repeatSeconds = $seconds;

return $this->everyMinute();
}






public function everyMinute()
{
return $this->spliceIntoPosition(1, '*');
}






public function everyTwoMinutes()
{
return $this->spliceIntoPosition(1, '*/2');
}






public function everyThreeMinutes()
{
return $this->spliceIntoPosition(1, '*/3');
}






public function everyFourMinutes()
{
return $this->spliceIntoPosition(1, '*/4');
}






public function everyFiveMinutes()
{
return $this->spliceIntoPosition(1, '*/5');
}






public function everyTenMinutes()
{
return $this->spliceIntoPosition(1, '*/10');
}






public function everyFifteenMinutes()
{
return $this->spliceIntoPosition(1, '*/15');
}






public function everyThirtyMinutes()
{
return $this->spliceIntoPosition(1, '*/30');
}






public function hourly()
{
return $this->spliceIntoPosition(1, 0);
}







public function hourlyAt($offset)
{
return $this->hourBasedSchedule($offset, '*');
}







public function everyOddHour($offset = 0)
{
return $this->hourBasedSchedule($offset, '1-23/2');
}







public function everyTwoHours($offset = 0)
{
return $this->hourBasedSchedule($offset, '*/2');
}







public function everyThreeHours($offset = 0)
{
return $this->hourBasedSchedule($offset, '*/3');
}







public function everyFourHours($offset = 0)
{
return $this->hourBasedSchedule($offset, '*/4');
}







public function everySixHours($offset = 0)
{
return $this->hourBasedSchedule($offset, '*/6');
}






public function daily()
{
return $this->hourBasedSchedule(0, 0);
}







public function at($time)
{
return $this->dailyAt($time);
}







public function dailyAt($time)
{
$segments = explode(':', $time);

return $this->hourBasedSchedule(
count($segments) >= 2 ? (int) $segments[1] : '0',
(int) $segments[0]
);
}








public function twiceDaily($first = 1, $second = 13)
{
return $this->twiceDailyAt($first, $second, 0);
}









public function twiceDailyAt($first = 1, $second = 13, $offset = 0)
{
$hours = $first.','.$second;

return $this->hourBasedSchedule($offset, $hours);
}








protected function hourBasedSchedule($minutes, $hours)
{
$minutes = is_array($minutes) ? implode(',', $minutes) : $minutes;

$hours = is_array($hours) ? implode(',', $hours) : $hours;

return $this->spliceIntoPosition(1, $minutes)
->spliceIntoPosition(2, $hours);
}






public function weekdays()
{
return $this->days(Schedule::MONDAY.'-'.Schedule::FRIDAY);
}






public function weekends()
{
return $this->days(Schedule::SATURDAY.','.Schedule::SUNDAY);
}






public function mondays()
{
return $this->days(Schedule::MONDAY);
}






public function tuesdays()
{
return $this->days(Schedule::TUESDAY);
}






public function wednesdays()
{
return $this->days(Schedule::WEDNESDAY);
}






public function thursdays()
{
return $this->days(Schedule::THURSDAY);
}






public function fridays()
{
return $this->days(Schedule::FRIDAY);
}






public function saturdays()
{
return $this->days(Schedule::SATURDAY);
}






public function sundays()
{
return $this->days(Schedule::SUNDAY);
}






public function weekly()
{
return $this->spliceIntoPosition(1, 0)
->spliceIntoPosition(2, 0)
->spliceIntoPosition(5, 0);
}








public function weeklyOn($dayOfWeek, $time = '0:0')
{
$this->dailyAt($time);

return $this->days($dayOfWeek);
}






public function monthly()
{
return $this->spliceIntoPosition(1, 0)
->spliceIntoPosition(2, 0)
->spliceIntoPosition(3, 1);
}








public function monthlyOn($dayOfMonth = 1, $time = '0:0')
{
$this->dailyAt($time);

return $this->spliceIntoPosition(3, $dayOfMonth);
}









public function twiceMonthly($first = 1, $second = 16, $time = '0:0')
{
$daysOfMonth = $first.','.$second;

$this->dailyAt($time);

return $this->spliceIntoPosition(3, $daysOfMonth);
}







public function lastDayOfMonth($time = '0:0')
{
$this->dailyAt($time);

return $this->spliceIntoPosition(3, Carbon::now()->endOfMonth()->day);
}






public function quarterly()
{
return $this->spliceIntoPosition(1, 0)
->spliceIntoPosition(2, 0)
->spliceIntoPosition(3, 1)
->spliceIntoPosition(4, '1-12/3');
}








public function quarterlyOn($dayOfQuarter = 1, $time = '0:0')
{
$this->dailyAt($time);

return $this->spliceIntoPosition(3, $dayOfQuarter)
->spliceIntoPosition(4, '1-12/3');
}






public function yearly()
{
return $this->spliceIntoPosition(1, 0)
->spliceIntoPosition(2, 0)
->spliceIntoPosition(3, 1)
->spliceIntoPosition(4, 1);
}









public function yearlyOn($month = 1, $dayOfMonth = 1, $time = '0:0')
{
$this->dailyAt($time);

return $this->spliceIntoPosition(3, $dayOfMonth)
->spliceIntoPosition(4, $month);
}







public function days($days)
{
$days = is_array($days) ? $days : func_get_args();

return $this->spliceIntoPosition(5, implode(',', $days));
}







public function timezone($timezone)
{
$this->timezone = enum_value($timezone);

return $this;
}








protected function spliceIntoPosition($position, $value)
{
$segments = preg_split("/\s+/", $this->expression);

$segments[$position - 1] = $value;

return $this->cron(implode(' ', $segments));
}
}
