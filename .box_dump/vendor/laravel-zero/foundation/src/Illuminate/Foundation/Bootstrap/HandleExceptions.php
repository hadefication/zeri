<?php

namespace Illuminate\Foundation\Bootstrap;

use ErrorException;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use Illuminate\Support\Env;
use Monolog\Handler\NullHandler;
use PHPUnit\Runner\ErrorHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Throwable;

class HandleExceptions
{





public static $reservedMemory;






protected static $app;







public function bootstrap(Application $app)
{
static::$reservedMemory = str_repeat('x', 32768);

static::$app = $app;

error_reporting(-1);

set_error_handler($this->forwardsTo('handleError'));

set_exception_handler($this->forwardsTo('handleException'));

register_shutdown_function($this->forwardsTo('handleShutdown'));

if (! $app->environment('testing')) {
ini_set('display_errors', 'Off');
}
}












public function handleError($level, $message, $file = '', $line = 0)
{
if ($this->isDeprecation($level)) {
$this->handleDeprecationError($message, $file, $line, $level);
} elseif (error_reporting() & $level) {
throw new ErrorException($message, 0, $level, $file, $line);
}
}










public function handleDeprecationError($message, $file, $line, $level = E_DEPRECATED)
{
if ($this->shouldIgnoreDeprecationErrors()) {
return;
}

try {
$logger = static::$app->make(LogManager::class);
} catch (Exception) {
return;
}

$this->ensureDeprecationLoggerIsConfigured();

$options = static::$app['config']->get('logging.deprecations') ?? [];

with($logger->channel('deprecations'), function ($log) use ($message, $file, $line, $level, $options) {
if ($options['trace'] ?? false) {
$log->warning((string) new ErrorException($message, 0, $level, $file, $line));
} else {
$log->warning(sprintf('%s in %s on line %s',
$message, $file, $line
));
}
});
}






protected function shouldIgnoreDeprecationErrors()
{
if (static::$app['log'] instanceof \Psr\Log\NullLogger) {
return true;
}

return ! class_exists(LogManager::class)
|| ! static::$app->hasBeenBootstrapped()
|| (static::$app->runningUnitTests() && ! Env::get('LOG_DEPRECATIONS_WHILE_TESTING'));
}






protected function ensureDeprecationLoggerIsConfigured()
{
with(static::$app['config'], function ($config) {
if ($config->get('logging.channels.deprecations')) {
return;
}

$this->ensureNullLogDriverIsConfigured();

if (is_array($options = $config->get('logging.deprecations'))) {
$driver = $options['channel'] ?? 'null';
} else {
$driver = $options ?? 'null';
}

$config->set('logging.channels.deprecations', $config->get("logging.channels.{$driver}"));
});
}






protected function ensureNullLogDriverIsConfigured()
{
with(static::$app['config'], function ($config) {
if ($config->get('logging.channels.null')) {
return;
}

$config->set('logging.channels.null', [
'driver' => 'monolog',
'handler' => NullHandler::class,
]);
});
}











public function handleException(Throwable $e)
{
static::$reservedMemory = null;

try {
$this->getExceptionHandler()->report($e);
} catch (Exception) {
$exceptionHandlerFailed = true;
}

if (static::$app->runningInConsole()) {
$this->renderForConsole($e);

if ($exceptionHandlerFailed ?? false) {
exit(1);
}
} else {
$this->renderHttpResponse($e);
}
}







protected function renderForConsole(Throwable $e)
{
$this->getExceptionHandler()->renderForConsole(new ConsoleOutput, $e);
}







protected function renderHttpResponse(Throwable $e)
{
$this->getExceptionHandler()->render(static::$app['request'], $e)->send();
}






public function handleShutdown()
{
static::$reservedMemory = null;

if (! is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
$this->handleException($this->fatalErrorFromPhpError($error, 0));
}
}








protected function fatalErrorFromPhpError(array $error, $traceOffset = null)
{
return new FatalError($error['message'], 0, $error, $traceOffset);
}






protected function forwardsTo($method)
{
return fn (...$arguments) => static::$app
? $this->{$method}(...$arguments)
: false;
}







protected function isDeprecation($level)
{
return in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]);
}







protected function isFatal($type)
{
return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
}






protected function getExceptionHandler()
{
return static::$app->make(ExceptionHandler::class);
}








public static function forgetApp()
{
static::$app = null;
}






public static function flushState()
{
if (is_null(static::$app)) {
return;
}

static::flushHandlersState();

static::$app = null;

static::$reservedMemory = null;
}






public static function flushHandlersState()
{
while (true) {
$previousHandler = set_exception_handler(static fn () => null);

restore_exception_handler();

if ($previousHandler === null) {
break;
}

restore_exception_handler();
}

while (true) {
$previousHandler = set_error_handler(static fn () => null);

restore_error_handler();

if ($previousHandler === null) {
break;
}

restore_error_handler();
}

if (class_exists(ErrorHandler::class)) {
$instance = ErrorHandler::instance();

if ((fn () => $this->enabled ?? false)->call($instance)) {
$instance->disable();
$instance->enable();
}
}
}
}
