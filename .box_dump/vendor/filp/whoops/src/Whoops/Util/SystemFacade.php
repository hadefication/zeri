<?php





namespace Whoops\Util;

class SystemFacade
{





public function startOutputBuffering()
{
return ob_start();
}







public function setErrorHandler(callable $handler, $types = 'use-php-defaults')
{

if ($types === 'use-php-defaults') {
$types = E_ALL;
}
return set_error_handler($handler, $types);
}






public function setExceptionHandler(callable $handler)
{
return set_exception_handler($handler);
}




public function restoreExceptionHandler()
{
restore_exception_handler();
}




public function restoreErrorHandler()
{
restore_error_handler();
}






public function registerShutdownFunction(callable $function)
{
register_shutdown_function($function);
}




public function cleanOutputBuffer()
{
return ob_get_clean();
}




public function getOutputBufferLevel()
{
return ob_get_level();
}




public function endOutputBuffering()
{
return ob_end_clean();
}




public function flushOutputBuffer()
{
flush();
}




public function getErrorReportingLevel()
{
return error_reporting();
}




public function getLastError()
{
return error_get_last();
}






public function setHttpResponseCode($httpCode)
{
if (!headers_sent()) {



header_remove('location');
}

return http_response_code($httpCode);
}




public function stopExecution($exitStatus)
{
exit($exitStatus);
}
}
