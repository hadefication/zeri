<?php

declare(strict_types=1);

namespace Dotenv\Util;

use GrahamCampbell\ResultType\Error;
use GrahamCampbell\ResultType\Success;




final class Regex
{







private function __construct()
{

}









public static function matches(string $pattern, string $subject)
{
return self::pregAndWrap(static function (string $subject) use ($pattern) {
return @\preg_match($pattern, $subject) === 1;
}, $subject);
}









public static function occurrences(string $pattern, string $subject)
{
return self::pregAndWrap(static function (string $subject) use ($pattern) {
return (int) @\preg_match_all($pattern, $subject);
}, $subject);
}











public static function replaceCallback(string $pattern, callable $callback, string $subject, ?int $limit = null)
{
return self::pregAndWrap(static function (string $subject) use ($pattern, $callback, $limit) {
return (string) @\preg_replace_callback($pattern, $callback, $subject, $limit ?? -1);
}, $subject);
}









public static function split(string $pattern, string $subject)
{
return self::pregAndWrap(static function (string $subject) use ($pattern) {

return (array) @\preg_split($pattern, $subject);
}, $subject);
}

/**
@template







*/
private static function pregAndWrap(callable $operation, string $subject)
{
$result = $operation($subject);

if (\preg_last_error() !== \PREG_NO_ERROR) {

return Error::create(\preg_last_error_msg());
}


return Success::create($result);
}
}
