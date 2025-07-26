<?php

namespace Illuminate\Support;

use Carbon\CarbonInterval;
use Closure;
use DateInterval;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;
use RuntimeException;

class Sleep
{
use Macroable;






public static $fakeSleepCallbacks = [];






protected static $syncWithCarbon = false;






public $duration;






public $while;






protected $pending = null;






protected static $fake = false;






protected static $sequence = [];






protected $shouldSleep = true;






protected $alreadySlept = false;






public function __construct($duration)
{
$this->duration($duration);
}







public static function for($duration)
{
return new static($duration);
}







public static function until($timestamp)
{
if (is_numeric($timestamp)) {
$timestamp = Carbon::createFromTimestamp($timestamp, date_default_timezone_get());
}

return new static(Carbon::now()->diff($timestamp));
}







public static function usleep($duration)
{
return (new static($duration))->microseconds();
}







public static function sleep($duration)
{
return (new static($duration))->seconds();
}







protected function duration($duration)
{
if (! $duration instanceof DateInterval) {
$this->duration = CarbonInterval::microsecond(0);

$this->pending = $duration;
} else {
$duration = CarbonInterval::instance($duration);

if ($duration->totalMicroseconds < 0) {
$duration = CarbonInterval::seconds(0);
}

$this->duration = $duration;
$this->pending = null;
}

return $this;
}






public function minutes()
{
$this->duration->add('minutes', $this->pullPending());

return $this;
}






public function minute()
{
return $this->minutes();
}






public function seconds()
{
$this->duration->add('seconds', $this->pullPending());

return $this;
}






public function second()
{
return $this->seconds();
}






public function milliseconds()
{
$this->duration->add('milliseconds', $this->pullPending());

return $this;
}






public function millisecond()
{
return $this->milliseconds();
}






public function microseconds()
{
$this->duration->add('microseconds', $this->pullPending());

return $this;
}






public function microsecond()
{
return $this->microseconds();
}







public function and($duration)
{
$this->pending = $duration;

return $this;
}







public function while(Closure $callback)
{
$this->while = $callback;

return $this;
}







public function then(callable $then)
{
$this->goodnight();

$this->alreadySlept = true;

return $then();
}






public function __destruct()
{
$this->goodnight();
}






protected function goodnight()
{
if ($this->alreadySlept || ! $this->shouldSleep) {
return;
}

if ($this->pending !== null) {
throw new RuntimeException('Unknown duration unit.');
}

if (static::$fake) {
static::$sequence[] = $this->duration;

if (static::$syncWithCarbon) {
Carbon::setTestNow(Carbon::now()->add($this->duration));
}

foreach (static::$fakeSleepCallbacks as $callback) {
$callback($this->duration);
}

return;
}

$remaining = $this->duration->copy();

$seconds = (int) $remaining->totalSeconds;

$while = $this->while ?: function () {
static $return = [true, false];

return array_shift($return);
};

while ($while()) {
if ($seconds > 0) {
sleep($seconds);

$remaining = $remaining->subSeconds($seconds);
}

$microseconds = (int) $remaining->totalMicroseconds;

if ($microseconds > 0) {
usleep($microseconds);
}
}
}






protected function pullPending()
{
if ($this->pending === null) {
$this->shouldNotSleep();

throw new RuntimeException('No duration specified.');
}

if ($this->pending < 0) {
$this->pending = 0;
}

return tap($this->pending, function () {
$this->pending = null;
});
}








public static function fake($value = true, $syncWithCarbon = false)
{
static::$fake = $value;

static::$sequence = [];
static::$fakeSleepCallbacks = [];
static::$syncWithCarbon = $syncWithCarbon;
}








public static function assertSlept($expected, $times = 1)
{
$count = (new Collection(static::$sequence))->filter($expected)->count();

PHPUnit::assertSame(
$times,
$count,
"The expected sleep was found [{$count}] times instead of [{$times}]."
);
}







public static function assertSleptTimes($expected)
{
PHPUnit::assertSame($expected, $count = count(static::$sequence), "Expected [{$expected}] sleeps but found [{$count}].");
}







public static function assertSequence($sequence)
{
static::assertSleptTimes(count($sequence));

(new Collection($sequence))
->zip(static::$sequence)
->eachSpread(function (?Sleep $expected, CarbonInterval $actual) {
if ($expected === null) {
return;
}

PHPUnit::assertTrue(
$expected->shouldNotSleep()->duration->equalTo($actual),
vsprintf('Expected sleep duration of [%s] but actually slept for [%s].', [
$expected->duration->cascade()->forHumans([
'options' => 0,
'minimumUnit' => 'microsecond',
]),
$actual->cascade()->forHumans([
'options' => 0,
'minimumUnit' => 'microsecond',
]),
])
);
});
}






public static function assertNeverSlept()
{
static::assertSleptTimes(0);
}






public static function assertInsomniac()
{
if (static::$sequence === []) {
PHPUnit::assertTrue(true);
}

foreach (static::$sequence as $duration) {
PHPUnit::assertSame(0, (int) $duration->totalMicroseconds, vsprintf('Unexpected sleep duration of [%s] found.', [
$duration->cascade()->forHumans([
'options' => 0,
'minimumUnit' => 'microsecond',
]),
]));
}
}






protected function shouldNotSleep()
{
$this->shouldSleep = false;

return $this;
}







public function when($condition)
{
$this->shouldSleep = (bool) value($condition, $this);

return $this;
}







public function unless($condition)
{
return $this->when(! value($condition, $this));
}







public static function whenFakingSleep($callback)
{
static::$fakeSleepCallbacks[] = $callback;
}






public static function syncWithCarbon($value = true)
{
static::$syncWithCarbon = $value;
}
}
