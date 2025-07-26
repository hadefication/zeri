<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use Dotenv\Util\Regex;
use Dotenv\Util\Str;

final class Lines
{







private function __construct()
{

}










public static function process(array $lines)
{
$output = [];
$multiline = false;
$multilineBuffer = [];

foreach ($lines as $line) {
[$multiline, $line, $multilineBuffer] = self::multilineProcess($multiline, $line, $multilineBuffer);

if (!$multiline && !self::isCommentOrWhitespace($line)) {
$output[] = $line;
}
}

return $output;
}










private static function multilineProcess(bool $multiline, string $line, array $buffer)
{
$startsOnCurrentLine = $multiline ? false : self::looksLikeMultilineStart($line);


if ($startsOnCurrentLine) {
$multiline = true;
}

if ($multiline) {
\array_push($buffer, $line);

if (self::looksLikeMultilineStop($line, $startsOnCurrentLine)) {
$multiline = false;
$line = \implode("\n", $buffer);
$buffer = [];
}
}

return [$multiline, $line, $buffer];
}








private static function looksLikeMultilineStart(string $line)
{
return Str::pos($line, '="')->map(static function () use ($line) {
return self::looksLikeMultilineStop($line, true) === false;
})->getOrElse(false);
}









private static function looksLikeMultilineStop(string $line, bool $started)
{
if ($line === '"') {
return true;
}

return Regex::occurrences('/(?=([^\\\\]"))/', \str_replace('\\\\', '', $line))->map(static function (int $count) use ($started) {
return $started ? $count > 1 : $count >= 1;
})->success()->getOrElse(false);
}








private static function isCommentOrWhitespace(string $line)
{
$line = \trim($line);

return $line === '' || (isset($line[0]) && $line[0] === '#');
}
}
