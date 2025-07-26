<?php

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Foundation\Testing\Wormhole;
use Illuminate\Support\Carbon;

trait InteractsWithTime
{






public function freezeTime($callback = null)
{
$result = $this->travelTo($now = Carbon::now(), $callback);

return is_null($callback) ? $now : $result;
}







public function freezeSecond($callback = null)
{
$result = $this->travelTo($now = Carbon::now()->startOfSecond(), $callback);

return is_null($callback) ? $now : $result;
}







public function travel($value)
{
return new Wormhole($value);
}








public function travelTo($date, $callback = null)
{
Carbon::setTestNow($date);

if ($callback) {
return tap($callback($date), function () {
Carbon::setTestNow();
});
}
}






public function travelBack()
{
return Wormhole::back();
}
}
