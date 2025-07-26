<?php

namespace Illuminate\Cache\RateLimiting;

class Limit
{





public $key;






public $maxAttempts;






public $decaySeconds;






public $responseCallback;








public function __construct($key = '', int $maxAttempts = 60, int $decaySeconds = 60)
{
$this->key = $key;
$this->maxAttempts = $maxAttempts;
$this->decaySeconds = $decaySeconds;
}








public static function perSecond($maxAttempts, $decaySeconds = 1)
{
return new static('', $maxAttempts, $decaySeconds);
}








public static function perMinute($maxAttempts, $decayMinutes = 1)
{
return new static('', $maxAttempts, 60 * $decayMinutes);
}








public static function perMinutes($decayMinutes, $maxAttempts)
{
return new static('', $maxAttempts, 60 * $decayMinutes);
}








public static function perHour($maxAttempts, $decayHours = 1)
{
return new static('', $maxAttempts, 60 * 60 * $decayHours);
}








public static function perDay($maxAttempts, $decayDays = 1)
{
return new static('', $maxAttempts, 60 * 60 * 24 * $decayDays);
}






public static function none()
{
return new Unlimited;
}







public function by($key)
{
$this->key = $key;

return $this;
}







public function response(callable $callback)
{
$this->responseCallback = $callback;

return $this;
}






public function fallbackKey()
{
$prefix = $this->key ? "{$this->key}:" : '';

return "{$prefix}attempts:{$this->maxAttempts}:decay:{$this->decaySeconds}";
}
}
