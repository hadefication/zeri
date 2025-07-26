<?php










namespace Symfony\Component\ErrorHandler;






class Debug
{
public static function enable(): ErrorHandler
{
error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
ini_set('display_errors', 0);
} elseif (!filter_var(\ini_get('log_errors'), \FILTER_VALIDATE_BOOL) || \ini_get('error_log')) {

ini_set('display_errors', 1);
}

@ini_set('zend.assertions', 1);
ini_set('assert.active', 1);
ini_set('assert.exception', 1);

DebugClassLoader::enable();

return ErrorHandler::register(new ErrorHandler(new BufferingLogger(), true));
}
}
