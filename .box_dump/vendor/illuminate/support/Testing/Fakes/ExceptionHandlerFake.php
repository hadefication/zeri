<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\Concerns\WithoutExceptionHandlingHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\Testing\Assert;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;
use Throwable;

/**
@mixin
*/
class ExceptionHandlerFake implements ExceptionHandler, Fake
{
use ForwardsCalls, ReflectsClosures;






protected $reported = [];






protected $throwOnReport = false;







public function __construct(
protected ExceptionHandler $handler,
protected array $exceptions = [],
) {

}






public function handler()
{
return $this->handler;
}







public function assertReported(Closure|string $exception)
{
$message = sprintf(
'The expected [%s] exception was not reported.',
is_string($exception) ? $exception : $this->firstClosureParameterType($exception)
);

if (is_string($exception)) {
Assert::assertTrue(
in_array($exception, array_map(get_class(...), $this->reported), true),
$message,
);

return;
}

Assert::assertTrue(
(new Collection($this->reported))->contains(
fn (Throwable $e) => $this->firstClosureParameterType($exception) === get_class($e)
&& $exception($e) === true,
), $message,
);
}







public function assertReportedCount(int $count)
{
$total = (new Collection($this->reported))->count();

PHPUnit::assertSame(
$count, $total,
"The total number of exceptions reported was {$total} instead of {$count}."
);
}







public function assertNotReported(Closure|string $exception)
{
try {
$this->assertReported($exception);
} catch (ExpectationFailedException $e) {
return;
}

throw new ExpectationFailedException(sprintf(
'The expected [%s] exception was reported.',
is_string($exception) ? $exception : $this->firstClosureParameterType($exception)
));
}






public function assertNothingReported()
{
Assert::assertEmpty(
$this->reported,
sprintf(
'The following exceptions were reported: %s.',
implode(', ', array_map(get_class(...), $this->reported)),
),
);
}







public function report($e)
{
if (! $this->isFakedException($e)) {
$this->handler->report($e);

return;
}

if (! $this->shouldReport($e)) {
return;
}

$this->reported[] = $e;

if ($this->throwOnReport) {
throw $e;
}
}







protected function isFakedException(Throwable $e)
{
return count($this->exceptions) === 0 || in_array(get_class($e), $this->exceptions, true);
}







public function shouldReport($e)
{
return $this->runningWithoutExceptionHandling() || $this->handler->shouldReport($e);
}






protected function runningWithoutExceptionHandling()
{
return $this->handler instanceof WithoutExceptionHandlingHandler;
}








public function render($request, $e)
{
return $this->handler->render($request, $e);
}








public function renderForConsole($output, Throwable $e)
{
$this->handler->renderForConsole($output, $e);
}






public function throwOnReport()
{
$this->throwOnReport = true;

return $this;
}








public function throwFirstReported()
{
foreach ($this->reported as $e) {
throw $e;
}

return $this;
}






public function reported()
{
return $this->reported;
}







public function setHandler(ExceptionHandler $handler)
{
$this->handler = $handler;

return $this;
}








public function __call(string $method, array $parameters)
{
return $this->forwardCallTo($this->handler, $method, $parameters);
}
}
