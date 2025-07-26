<?php declare(strict_types=1);








namespace SebastianBergmann\Invoker;

use const SIGALRM;
use function call_user_func_array;
use function extension_loaded;
use function function_exists;
use function pcntl_alarm;
use function pcntl_async_signals;
use function pcntl_signal;
use function sprintf;
use Throwable;

final class Invoker
{





public function invoke(callable $callable, array $arguments, int $timeout): mixed
{
if (!$this->canInvokeWithTimeout()) {

throw new ProcessControlExtensionNotLoadedException;

}

pcntl_signal(
SIGALRM,
static function () use ($timeout): void
{
throw new TimeoutException(
sprintf(
'Execution aborted after %d second%s',
$timeout,
$timeout === 1 ? '' : 's',
),
);
},
);

pcntl_async_signals(true);
pcntl_alarm($timeout);

try {
return call_user_func_array($callable, $arguments);
} finally {
pcntl_alarm(0);
}
}

public function canInvokeWithTimeout(): bool
{
return extension_loaded('pcntl') && function_exists('pcntl_signal') && function_exists('pcntl_async_signals') && function_exists('pcntl_alarm');
}
}
