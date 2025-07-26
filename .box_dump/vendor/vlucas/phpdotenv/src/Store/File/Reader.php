<?php

declare(strict_types=1);

namespace Dotenv\Store\File;

use Dotenv\Exception\InvalidEncodingException;
use Dotenv\Util\Str;
use PhpOption\Option;




final class Reader
{







private function __construct()
{

}
















public static function read(array $filePaths, bool $shortCircuit = true, ?string $fileEncoding = null)
{
$output = [];

foreach ($filePaths as $filePath) {
$content = self::readFromFile($filePath, $fileEncoding);
if ($content->isDefined()) {
$output[$filePath] = $content->get();
if ($shortCircuit) {
break;
}
}
}

return $output;
}











private static function readFromFile(string $path, ?string $encoding = null)
{

$content = Option::fromValue(@\file_get_contents($path), false);

return $content->flatMap(static function (string $content) use ($encoding) {
return Str::utf8($content, $encoding)->mapError(static function (string $error) {
throw new InvalidEncodingException($error);
})->success();
});
}
}
