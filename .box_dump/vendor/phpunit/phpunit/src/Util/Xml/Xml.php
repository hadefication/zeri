<?php declare(strict_types=1);








namespace PHPUnit\Util;

use const ENT_QUOTES;
use function htmlspecialchars;
use function mb_convert_encoding;
use function ord;
use function preg_replace;
use function strlen;

/**
@no-named-arguments


*/
final readonly class Xml
{








public static function prepareString(string $string): string
{
return preg_replace(
'/[\\x00-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]/',
'',
htmlspecialchars(
self::convertToUtf8($string),
ENT_QUOTES,
),
);
}

private static function convertToUtf8(string $string): string
{
if (!self::isUtf8($string)) {
$string = mb_convert_encoding($string, 'UTF-8');
}

return $string;
}

private static function isUtf8(string $string): bool
{
$length = strlen($string);

for ($i = 0; $i < $length; $i++) {
if (ord($string[$i]) < 0x80) {
$n = 0;
} elseif ((ord($string[$i]) & 0xE0) === 0xC0) {
$n = 1;
} elseif ((ord($string[$i]) & 0xF0) === 0xE0) {
$n = 2;
} elseif ((ord($string[$i]) & 0xF0) === 0xF0) {
$n = 3;
} else {
return false;
}

for ($j = 0; $j < $n; $j++) {
if ((++$i === $length) || ((ord($string[$i]) & 0xC0) !== 0x80)) {
return false;
}
}
}

return true;
}
}
