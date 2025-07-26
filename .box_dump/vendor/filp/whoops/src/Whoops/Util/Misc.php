<?php





namespace Whoops\Util;

class Misc
{










public static function canSendHeaders()
{
return isset($_SERVER["REQUEST_URI"]) && !headers_sent();
}

public static function isAjaxRequest()
{
return (
!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}





public static function isCommandLine()
{
return PHP_SAPI == 'cli';
}







public static function translateErrorCode($error_code)
{
$constants = get_defined_constants(true);
if (array_key_exists('Core', $constants)) {
foreach ($constants['Core'] as $constant => $value) {
if (substr($constant, 0, 2) == 'E_' && $value == $error_code) {
return $constant;
}
}
}
return "E_UNKNOWN";
}







public static function isLevelFatal($level)
{
$errors = E_ERROR;
$errors |= E_PARSE;
$errors |= E_CORE_ERROR;
$errors |= E_CORE_WARNING;
$errors |= E_COMPILE_ERROR;
$errors |= E_COMPILE_WARNING;
return ($level & $errors) > 0;
}
}
