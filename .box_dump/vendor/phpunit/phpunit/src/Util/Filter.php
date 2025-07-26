<?php declare(strict_types=1);








namespace PHPUnit\Util;

use function array_unshift;
use function defined;
use function in_array;
use function is_file;
use function realpath;
use function sprintf;
use function str_starts_with;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\PhptAssertionFailedError;
use Throwable;

/**
@no-named-arguments


*/
final readonly class Filter
{



public static function stackTraceFromThrowableAsString(Throwable $t, bool $unwrap = true): string
{
if ($t instanceof PhptAssertionFailedError) {
$stackTrace = $t->syntheticTrace();
$file = $t->syntheticFile();
$line = $t->syntheticLine();
} elseif ($t instanceof Exception) {
$stackTrace = $t->getSerializableTrace();
$file = $t->getFile();
$line = $t->getLine();
} else {
if ($unwrap && $t->getPrevious()) {
$t = $t->getPrevious();
}

$stackTrace = $t->getTrace();
$file = $t->getFile();
$line = $t->getLine();
}

if (!self::frameExists($stackTrace, $file, $line)) {
array_unshift(
$stackTrace,
['file' => $file, 'line' => $line],
);
}

return self::stackTraceAsString($stackTrace);
}




public static function stackTraceAsString(array $frames): string
{
$buffer = '';
$prefix = defined('__PHPUNIT_PHAR_ROOT__') ? __PHPUNIT_PHAR_ROOT__ : false;
$excludeList = new ExcludeList;

foreach ($frames as $frame) {
if (self::shouldPrintFrame($frame, $prefix, $excludeList)) {
$buffer .= sprintf(
"%s:%s\n",
$frame['file'],
$frame['line'] ?? '?',
);
}
}

return $buffer;
}




private static function shouldPrintFrame(array $frame, false|string $prefix, ExcludeList $excludeList): bool
{
if (!isset($frame['file'])) {
return false;
}

$file = $frame['file'];
$fileIsNotPrefixed = $prefix === false || !str_starts_with($file, $prefix);


if (isset($GLOBALS['_SERVER']['SCRIPT_NAME'])) {
$script = realpath($GLOBALS['_SERVER']['SCRIPT_NAME']);
} else {

$script = '';

}

return $fileIsNotPrefixed &&
$file !== $script &&
self::fileIsExcluded($file, $excludeList) &&
is_file($file);
}

private static function fileIsExcluded(string $file, ExcludeList $excludeList): bool
{
return (empty($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST']) ||
!in_array($file, $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], true)) &&
!$excludeList->isExcluded($file);
}




private static function frameExists(array $trace, string $file, int $line): bool
{
foreach ($trace as $frame) {
if (isset($frame['file'], $frame['line']) && $frame['file'] === $file && $frame['line'] === $line) {
return true;
}
}

return false;
}
}
