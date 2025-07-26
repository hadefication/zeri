<?php










namespace Joli\JoliNotif\Exception;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0.', NoSupportedNotifierException::class);




class NoSupportedNotifierException extends \RuntimeException implements Exception
{
public function __construct(
string $message = 'No supported notifier',
int $code = 0,
?\Throwable $previous = null,
) {
parent::__construct($message, $code, $previous);
}
}
