<?php

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Lock as LockContract;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

abstract class Lock implements LockContract
{
use InteractsWithTime;






protected $name;






protected $seconds;






protected $owner;






protected $sleepMilliseconds = 250;








public function __construct($name, $seconds, $owner = null)
{
if (is_null($owner)) {
$owner = Str::random();
}

$this->name = $name;
$this->owner = $owner;
$this->seconds = $seconds;
}






abstract public function acquire();






abstract public function release();






abstract protected function getCurrentOwner();







public function get($callback = null)
{
$result = $this->acquire();

if ($result && is_callable($callback)) {
try {
return $callback();
} finally {
$this->release();
}
}

return $result;
}










public function block($seconds, $callback = null)
{
$starting = ((int) now()->format('Uu')) / 1000;

$milliseconds = $seconds * 1000;

while (! $this->acquire()) {
$now = ((int) now()->format('Uu')) / 1000;

if (($now + $this->sleepMilliseconds - $milliseconds) >= $starting) {
throw new LockTimeoutException;
}

Sleep::usleep($this->sleepMilliseconds * 1000);
}

if (is_callable($callback)) {
try {
return $callback();
} finally {
$this->release();
}
}

return true;
}






public function owner()
{
return $this->owner;
}






public function isOwnedByCurrentProcess()
{
return $this->isOwnedBy($this->owner);
}







public function isOwnedBy($owner)
{
return $this->getCurrentOwner() === $owner;
}







public function betweenBlockedAttemptsSleepFor($milliseconds)
{
$this->sleepMilliseconds = $milliseconds;

return $this;
}
}
