<?php declare(strict_types=1);








namespace SebastianBergmann\Invoker;

use function extension_loaded;
use function function_exists;
use function implode;
use RuntimeException;

final class ProcessControlExtensionNotLoadedException extends RuntimeException implements Exception
{
public function __construct()
{
$message = [];

if (!extension_loaded('pcntl')) {
$message[] = 'The pcntl (process control) extension for PHP must be loaded.';
}

if (!function_exists('pcntl_signal')) {
$message[] = 'The pcntl_signal() function must not be disabled.';
}

if (!function_exists('pcntl_async_signals')) {
$message[] = 'The pcntl_async_signals() function must not be disabled.';
}

if (!function_exists('pcntl_alarm')) {
$message[] = 'The pcntl_alarm() function must not be disabled.';
}

parent::__construct(implode(PHP_EOL, $message));
}
}
