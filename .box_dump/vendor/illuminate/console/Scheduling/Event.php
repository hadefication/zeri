<?php

namespace Illuminate\Console\Scheduling;

use Closure;
use Cron\CronExpression;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Console\Application;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use Illuminate\Support\Traits\Tappable;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Process\Process;
use Throwable;

class Event
{
use Macroable, ManagesAttributes, ManagesFrequencies, ReflectsClosures, Tappable;






public $command;






public $output = '/dev/null';






public $shouldAppendOutput = false;






protected $beforeCallbacks = [];






protected $afterCallbacks = [];






public $mutex;






public $mutexNameResolver;








protected $lastChecked;






public $exitCode;








public function __construct(EventMutex $mutex, $command, $timezone = null)
{
$this->mutex = $mutex;
$this->command = $command;
$this->timezone = $timezone;

$this->output = $this->getDefaultOutput();
}






public function getDefaultOutput()
{
return (DIRECTORY_SEPARATOR === '\\') ? 'NUL' : '/dev/null';
}









public function run(Container $container)
{
if ($this->shouldSkipDueToOverlapping()) {
return;
}

$exitCode = $this->start($container);

if (! $this->runInBackground) {
$this->finish($container, $exitCode);
}
}






public function shouldSkipDueToOverlapping()
{
return $this->withoutOverlapping && ! $this->mutex->create($this);
}






public function isRepeatable()
{
return ! is_null($this->repeatSeconds);
}






public function shouldRepeatNow()
{
return $this->isRepeatable()
&& $this->lastChecked?->diffInSeconds() >= $this->repeatSeconds;
}









protected function start($container)
{
try {
$this->callBeforeCallbacks($container);

return $this->execute($container);
} catch (Throwable $exception) {
$this->removeMutex();

throw $exception;
}
}







protected function execute($container)
{
return Process::fromShellCommandline(
$this->buildCommand(), base_path(), null, null, null
)->run(
laravel_cloud()
? fn ($type, $line) => fwrite($type === 'out' ? STDOUT : STDERR, $line)
: fn () => true
);
}








public function finish(Container $container, $exitCode)
{
$this->exitCode = (int) $exitCode;

try {
$this->callAfterCallbacks($container);
} finally {
$this->removeMutex();
}
}







public function callBeforeCallbacks(Container $container)
{
foreach ($this->beforeCallbacks as $callback) {
$container->call($callback);
}
}







public function callAfterCallbacks(Container $container)
{
foreach ($this->afterCallbacks as $callback) {
$container->call($callback);
}
}






public function buildCommand()
{
return (new CommandBuilder)->buildCommand($this);
}







public function isDue($app)
{
if (! $this->runsInMaintenanceMode() && $app->isDownForMaintenance()) {
return false;
}

return $this->expressionPasses() &&
$this->runsInEnvironment($app->environment());
}






public function runsInMaintenanceMode()
{
return $this->evenInMaintenanceMode;
}






protected function expressionPasses()
{
$date = Date::now();

if ($this->timezone) {
$date = $date->setTimezone($this->timezone);
}

return (new CronExpression($this->expression))->isDue($date->toDateTimeString());
}







public function runsInEnvironment($environment)
{
return empty($this->environments) || in_array($environment, $this->environments);
}







public function filtersPass($app)
{
$this->lastChecked = Date::now();

foreach ($this->filters as $callback) {
if (! $app->call($callback)) {
return false;
}
}

foreach ($this->rejects as $callback) {
if ($app->call($callback)) {
return false;
}
}

return true;
}






public function storeOutput()
{
$this->ensureOutputIsBeingCaptured();

return $this;
}








public function sendOutputTo($location, $append = false)
{
$this->output = $location;

$this->shouldAppendOutput = $append;

return $this;
}







public function appendOutputTo($location)
{
return $this->sendOutputTo($location, true);
}










public function emailOutputTo($addresses, $onlyIfOutputExists = true)
{
$this->ensureOutputIsBeingCaptured();

$addresses = Arr::wrap($addresses);

return $this->then(function (Mailer $mailer) use ($addresses, $onlyIfOutputExists) {
$this->emailOutput($mailer, $addresses, $onlyIfOutputExists);
});
}









public function emailWrittenOutputTo($addresses)
{
return $this->emailOutputTo($addresses, true);
}







public function emailOutputOnFailure($addresses)
{
$this->ensureOutputIsBeingCaptured();

$addresses = Arr::wrap($addresses);

return $this->onFailure(function (Mailer $mailer) use ($addresses) {
$this->emailOutput($mailer, $addresses, false);
});
}






protected function ensureOutputIsBeingCaptured()
{
if (is_null($this->output) || $this->output == $this->getDefaultOutput()) {
$this->sendOutputTo(storage_path('logs/schedule-'.sha1($this->mutexName()).'.log'));
}
}









protected function emailOutput(Mailer $mailer, $addresses, $onlyIfOutputExists = true)
{
$text = is_file($this->output) ? file_get_contents($this->output) : '';

if ($onlyIfOutputExists && empty($text)) {
return;
}

$mailer->raw($text, function ($m) use ($addresses) {
$m->to($addresses)->subject($this->getEmailSubject());
});
}






protected function getEmailSubject()
{
if ($this->description) {
return $this->description;
}

return "Scheduled Job Output For [{$this->command}]";
}







public function pingBefore($url)
{
return $this->before($this->pingCallback($url));
}








public function pingBeforeIf($value, $url)
{
return $value ? $this->pingBefore($url) : $this;
}







public function thenPing($url)
{
return $this->then($this->pingCallback($url));
}








public function thenPingIf($value, $url)
{
return $value ? $this->thenPing($url) : $this;
}







public function pingOnSuccess($url)
{
return $this->onSuccess($this->pingCallback($url));
}








public function pingOnSuccessIf($value, $url)
{
return $value ? $this->onSuccess($this->pingCallback($url)) : $this;
}







public function pingOnFailure($url)
{
return $this->onFailure($this->pingCallback($url));
}








public function pingOnFailureIf($value, $url)
{
return $value ? $this->onFailure($this->pingCallback($url)) : $this;
}







protected function pingCallback($url)
{
return function (Container $container) use ($url) {
try {
$this->getHttpClient($container)->request('GET', $url);
} catch (ClientExceptionInterface|TransferException $e) {
$container->make(ExceptionHandler::class)->report($e);
}
};
}







protected function getHttpClient(Container $container)
{
return match (true) {
$container->bound(HttpClientInterface::class) => $container->make(HttpClientInterface::class),
$container->bound(HttpClient::class) => $container->make(HttpClient::class),
default => new HttpClient([
'connect_timeout' => 10,
'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
'timeout' => 30,
]),
};
}







public function before(Closure $callback)
{
$this->beforeCallbacks[] = $callback;

return $this;
}







public function after(Closure $callback)
{
return $this->then($callback);
}







public function then(Closure $callback)
{
$parameters = $this->closureParameterTypes($callback);

if (Arr::get($parameters, 'output') === Stringable::class) {
return $this->thenWithOutput($callback);
}

$this->afterCallbacks[] = $callback;

return $this;
}








public function thenWithOutput(Closure $callback, $onlyIfOutputExists = false)
{
$this->ensureOutputIsBeingCaptured();

return $this->then($this->withOutputCallback($callback, $onlyIfOutputExists));
}







public function onSuccess(Closure $callback)
{
$parameters = $this->closureParameterTypes($callback);

if (Arr::get($parameters, 'output') === Stringable::class) {
return $this->onSuccessWithOutput($callback);
}

return $this->then(function (Container $container) use ($callback) {
if ($this->exitCode === 0) {
$container->call($callback);
}
});
}








public function onSuccessWithOutput(Closure $callback, $onlyIfOutputExists = false)
{
$this->ensureOutputIsBeingCaptured();

return $this->onSuccess($this->withOutputCallback($callback, $onlyIfOutputExists));
}







public function onFailure(Closure $callback)
{
$parameters = $this->closureParameterTypes($callback);

if (Arr::get($parameters, 'output') === Stringable::class) {
return $this->onFailureWithOutput($callback);
}

return $this->then(function (Container $container) use ($callback) {
if ($this->exitCode !== 0) {
$container->call($callback);
}
});
}








public function onFailureWithOutput(Closure $callback, $onlyIfOutputExists = false)
{
$this->ensureOutputIsBeingCaptured();

return $this->onFailure($this->withOutputCallback($callback, $onlyIfOutputExists));
}








protected function withOutputCallback(Closure $callback, $onlyIfOutputExists = false)
{
return function (Container $container) use ($callback, $onlyIfOutputExists) {
$output = $this->output && is_file($this->output) ? file_get_contents($this->output) : '';

return $onlyIfOutputExists && empty($output)
? null
: $container->call($callback, ['output' => new Stringable($output)]);
};
}






public function getSummaryForDisplay()
{
if (is_string($this->description)) {
return $this->description;
}

return $this->buildCommand();
}









public function nextRunDate($currentTime = 'now', $nth = 0, $allowCurrentDate = false)
{
return Date::instance((new CronExpression($this->getExpression()))
->getNextRunDate($currentTime, $nth, $allowCurrentDate, $this->timezone));
}






public function getExpression()
{
return $this->expression;
}







public function preventOverlapsUsing(EventMutex $mutex)
{
$this->mutex = $mutex;

return $this;
}






public function mutexName()
{
$mutexNameResolver = $this->mutexNameResolver;

if (! is_null($mutexNameResolver) && is_callable($mutexNameResolver)) {
return $mutexNameResolver($this);
}

return 'framework'.DIRECTORY_SEPARATOR.'schedule-'.
sha1($this->expression.$this->normalizeCommand($this->command ?? ''));
}







public function createMutexNameUsing(Closure|string $mutexName)
{
$this->mutexNameResolver = is_string($mutexName) ? fn () => $mutexName : $mutexName;

return $this;
}






protected function removeMutex()
{
if ($this->withoutOverlapping) {
$this->mutex->forget($this);
}
}







public static function normalizeCommand($command)
{
return str_replace([
Application::phpBinary(),
Application::artisanBinary(),
], [
'php',
preg_replace("#['\"]#", '', Application::artisanBinary()),
], $command);
}
}
