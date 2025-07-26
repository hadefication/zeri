<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Support\Carbon;

class Wormhole
{





public $value;






public function __construct($value)
{
$this->value = $value;
}







public function microsecond($callback = null)
{
return $this->microseconds($callback);
}







public function microseconds($callback = null)
{
Carbon::setTestNow(Carbon::now()->addMicroseconds($this->value));

return $this->handleCallback($callback);
}







public function millisecond($callback = null)
{
return $this->milliseconds($callback);
}







public function milliseconds($callback = null)
{
Carbon::setTestNow(Carbon::now()->addMilliseconds($this->value));

return $this->handleCallback($callback);
}







public function second($callback = null)
{
return $this->seconds($callback);
}







public function seconds($callback = null)
{
Carbon::setTestNow(Carbon::now()->addSeconds($this->value));

return $this->handleCallback($callback);
}







public function minute($callback = null)
{
return $this->minutes($callback);
}







public function minutes($callback = null)
{
Carbon::setTestNow(Carbon::now()->addMinutes($this->value));

return $this->handleCallback($callback);
}







public function hour($callback = null)
{
return $this->hours($callback);
}







public function hours($callback = null)
{
Carbon::setTestNow(Carbon::now()->addHours($this->value));

return $this->handleCallback($callback);
}







public function day($callback = null)
{
return $this->days($callback);
}







public function days($callback = null)
{
Carbon::setTestNow(Carbon::now()->addDays($this->value));

return $this->handleCallback($callback);
}







public function week($callback = null)
{
return $this->weeks($callback);
}







public function weeks($callback = null)
{
Carbon::setTestNow(Carbon::now()->addWeeks($this->value));

return $this->handleCallback($callback);
}







public function month($callback = null)
{
return $this->months($callback);
}







public function months($callback = null)
{
Carbon::setTestNow(Carbon::now()->addMonths($this->value));

return $this->handleCallback($callback);
}







public function year($callback = null)
{
return $this->years($callback);
}







public function years($callback = null)
{
Carbon::setTestNow(Carbon::now()->addYears($this->value));

return $this->handleCallback($callback);
}






public static function back()
{
Carbon::setTestNow();

return Carbon::now();
}







protected function handleCallback($callback)
{
if ($callback) {
return tap($callback(), function () {
Carbon::setTestNow();
});
}
}
}
