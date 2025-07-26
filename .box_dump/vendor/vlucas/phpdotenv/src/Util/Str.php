<?php

declare(strict_types=1);

namespace Dotenv\Util;

use GrahamCampbell\ResultType\Error;
use GrahamCampbell\ResultType\Success;
use PhpOption\Option;




final class Str
{







private function __construct()
{

}









public static function utf8(string $input, ?string $encoding = null)
{
if ($encoding !== null && !\in_array($encoding, \mb_list_encodings(), true)) {

return Error::create(
\sprintf('Illegal character encoding [%s] specified.', $encoding)
);
}

$converted = $encoding === null ?
@\mb_convert_encoding($input, 'UTF-8') :
@\mb_convert_encoding($input, 'UTF-8', $encoding);

if (!is_string($converted)) {

return Error::create(
\sprintf('Conversion from encoding [%s] failed.', $encoding ?? 'NULL')
);
}






if (\substr($converted, 0, 3) == "\xEF\xBB\xBF") {
$converted = \substr($converted, 3);
}


return Success::create($converted);
}









public static function pos(string $haystack, string $needle)
{

return Option::fromValue(\mb_strpos($haystack, $needle, 0, 'UTF-8'), false);
}










public static function substr(string $input, int $start, ?int $length = null)
{
return \mb_substr($input, $start, $length, 'UTF-8');
}








public static function len(string $input)
{
return \mb_strlen($input, 'UTF-8');
}
}
