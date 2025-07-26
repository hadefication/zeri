<?php

namespace Illuminate\Support;

use RuntimeException;

class Lottery
{





protected $chances;






protected $outOf;






protected $winner;






protected $loser;






protected static $resultFactory;







public function __construct($chances, $outOf = null)
{
if ($outOf === null && is_float($chances) && $chances > 1) {
throw new RuntimeException('Float must not be greater than 1.');
}

if ($outOf !== null && $outOf < 1) {
throw new RuntimeException('Lottery "out of" value must be greater than or equal to 1.');
}

$this->chances = $chances;

$this->outOf = $outOf;
}








public static function odds($chances, $outOf = null)
{
return new static($chances, $outOf);
}







public function winner($callback)
{
$this->winner = $callback;

return $this;
}







public function loser($callback)
{
$this->loser = $callback;

return $this;
}







public function __invoke(...$args)
{
return $this->runCallback(...$args);
}







public function choose($times = null)
{
if ($times === null) {
return $this->runCallback();
}

$results = [];

for ($i = 0; $i < $times; $i++) {
$results[] = $this->runCallback();
}

return $results;
}







protected function runCallback(...$args)
{
return $this->wins()
? ($this->winner ?? fn () => true)(...$args)
: ($this->loser ?? fn () => false)(...$args);
}






protected function wins()
{
return static::resultFactory()($this->chances, $this->outOf);
}






protected static function resultFactory()
{
return static::$resultFactory ?? fn ($chances, $outOf) => $outOf === null
? random_int(0, PHP_INT_MAX) / PHP_INT_MAX <= $chances
: random_int(1, $outOf) <= $chances;
}







public static function alwaysWin($callback = null)
{
self::setResultFactory(fn () => true);

if ($callback === null) {
return;
}

$callback();

static::determineResultNormally();
}







public static function alwaysLose($callback = null)
{
self::setResultFactory(fn () => false);

if ($callback === null) {
return;
}

$callback();

static::determineResultNormally();
}








public static function fix($sequence, $whenMissing = null)
{
static::forceResultWithSequence($sequence, $whenMissing);
}








public static function forceResultWithSequence($sequence, $whenMissing = null)
{
$next = 0;

$whenMissing ??= function ($chances, $outOf) use (&$next) {
$factoryCache = static::$resultFactory;

static::$resultFactory = null;

$result = static::resultFactory()($chances, $outOf);

static::$resultFactory = $factoryCache;

$next++;

return $result;
};

static::setResultFactory(function ($chances, $outOf) use (&$next, $sequence, $whenMissing) {
if (array_key_exists($next, $sequence)) {
return $sequence[$next++];
}

return $whenMissing($chances, $outOf);
});
}






public static function determineResultsNormally()
{
static::determineResultNormally();
}






public static function determineResultNormally()
{
static::$resultFactory = null;
}







public static function setResultFactory($factory)
{
self::$resultFactory = $factory;
}
}
