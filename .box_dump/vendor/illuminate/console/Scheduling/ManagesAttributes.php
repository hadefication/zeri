<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Support\Reflector;

trait ManagesAttributes
{





public $expression = '* * * * *';






public $repeatSeconds = null;






public $timezone;






public $user;






public $environments = [];






public $evenInMaintenanceMode = false;






public $withoutOverlapping = false;






public $onOneServer = false;






public $expiresAt = 1440;






public $runInBackground = false;






protected $filters = [];






protected $rejects = [];






public $description;







public function user($user)
{
$this->user = $user;

return $this;
}







public function environments($environments)
{
$this->environments = is_array($environments) ? $environments : func_get_args();

return $this;
}






public function evenInMaintenanceMode()
{
$this->evenInMaintenanceMode = true;

return $this;
}








public function withoutOverlapping($expiresAt = 1440)
{
$this->withoutOverlapping = true;

$this->expiresAt = $expiresAt;

return $this->skip(function () {
return $this->mutex->exists($this);
});
}






public function onOneServer()
{
$this->onOneServer = true;

return $this;
}






public function runInBackground()
{
$this->runInBackground = true;

return $this;
}







public function when($callback)
{
$this->filters[] = Reflector::isCallable($callback) ? $callback : function () use ($callback) {
return $callback;
};

return $this;
}







public function skip($callback)
{
$this->rejects[] = Reflector::isCallable($callback) ? $callback : function () use ($callback) {
return $callback;
};

return $this;
}







public function name($description)
{
return $this->description($description);
}







public function description($description)
{
$this->description = $description;

return $this;
}
}
