<?php

namespace Illuminate\Foundation\Exceptions;

use Illuminate\Support\Traits\ReflectsClosures;
use Throwable;

class ReportableHandler
{
use ReflectsClosures;






protected $callback;






protected $shouldStop = false;






public function __construct(callable $callback)
{
$this->callback = $callback;
}







public function __invoke(Throwable $e)
{
$result = call_user_func($this->callback, $e);

if ($result === false) {
return false;
}

return ! $this->shouldStop;
}







public function handles(Throwable $e)
{
foreach ($this->firstClosureParameterTypes($this->callback) as $type) {
if (is_a($e, $type)) {
return true;
}
}

return false;
}






public function stop()
{
$this->shouldStop = true;

return $this;
}
}
