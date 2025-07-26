<?php

namespace Illuminate\Foundation\Configuration;

use Closure;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;

class Exceptions
{





public function __construct(public Handler $handler)
{
}







public function report(callable $using)
{
return $this->handler->reportable($using);
}







public function reportable(callable $reportUsing)
{
return $this->handler->reportable($reportUsing);
}







public function render(callable $using)
{
$this->handler->renderable($using);

return $this;
}







public function renderable(callable $renderUsing)
{
$this->handler->renderable($renderUsing);

return $this;
}







public function respond(callable $using)
{
$this->handler->respondUsing($using);

return $this;
}







public function throttle(callable $throttleUsing)
{
$this->handler->throttleUsing($throttleUsing);

return $this;
}










public function map($from, $to = null)
{
$this->handler->map($from, $to);

return $this;
}








public function level(string $type, string $level)
{
$this->handler->level($type, $level);

return $this;
}







public function context(Closure $contextCallback)
{
$this->handler->buildContextUsing($contextCallback);

return $this;
}







public function dontReport(array|string $class)
{
foreach (Arr::wrap($class) as $exceptionClass) {
$this->handler->dontReport($exceptionClass);
}

return $this;
}






public function dontReportDuplicates()
{
$this->handler->dontReportDuplicates();

return $this;
}







public function dontFlash(array|string $attributes)
{
$this->handler->dontFlash($attributes);

return $this;
}







public function shouldRenderJsonWhen(callable $callback)
{
$this->handler->shouldRenderJsonWhen($callback);

return $this;
}







public function stopIgnoring(array|string $class)
{
$this->handler->stopIgnoring($class);

return $this;
}







public function truncateRequestExceptionsAt(int $length)
{
RequestException::truncateAt($length);

return $this;
}






public function dontTruncateRequestExceptions()
{
RequestException::dontTruncate();

return $this;
}
}
