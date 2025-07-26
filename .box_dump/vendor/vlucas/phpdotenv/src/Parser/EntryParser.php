<?php

declare(strict_types=1);

namespace Dotenv\Parser;

use Dotenv\Util\Regex;
use Dotenv\Util\Str;
use GrahamCampbell\ResultType\Error;
use GrahamCampbell\ResultType\Result;
use GrahamCampbell\ResultType\Success;

final class EntryParser
{
private const INITIAL_STATE = 0;
private const UNQUOTED_STATE = 1;
private const SINGLE_QUOTED_STATE = 2;
private const DOUBLE_QUOTED_STATE = 3;
private const ESCAPE_SEQUENCE_STATE = 4;
private const WHITESPACE_STATE = 5;
private const COMMENT_STATE = 6;
private const REJECT_STATES = [self::SINGLE_QUOTED_STATE, self::DOUBLE_QUOTED_STATE, self::ESCAPE_SEQUENCE_STATE];








private function __construct()
{

}











public static function parse(string $entry)
{
return self::splitStringIntoParts($entry)->flatMap(static function (array $parts) {
[$name, $value] = $parts;

return self::parseName($name)->flatMap(static function (string $name) use ($value) {

$parsedValue = $value === null ? Success::create(null) : self::parseValue($value);

return $parsedValue->map(static function (?Value $value) use ($name) {
return new Entry($name, $value);
});
});
});
}








private static function splitStringIntoParts(string $line)
{

$result = Str::pos($line, '=')->map(static function () use ($line) {
return \array_map('trim', \explode('=', $line, 2));
})->getOrElse([$line, null]);

if ($result[0] === '') {

return Error::create(self::getErrorMessage('an unexpected equals', $line));
}


return Success::create($result);
}











private static function parseName(string $name)
{
if (Str::len($name) > 8 && Str::substr($name, 0, 6) === 'export' && \ctype_space(Str::substr($name, 6, 1))) {
$name = \ltrim(Str::substr($name, 6));
}

if (self::isQuotedName($name)) {
$name = Str::substr($name, 1, -1);
}

if (!self::isValidName($name)) {

return Error::create(self::getErrorMessage('an invalid name', $name));
}


return Success::create($name);
}








private static function isQuotedName(string $name)
{
if (Str::len($name) < 3) {
return false;
}

$first = Str::substr($name, 0, 1);
$last = Str::substr($name, -1, 1);

return ($first === '"' && $last === '"') || ($first === '\'' && $last === '\'');
}








private static function isValidName(string $name)
{
return Regex::matches('~(*UTF8)\A[\p{Ll}\p{Lu}\p{M}\p{N}_.]+\z~', $name)->success()->getOrElse(false);
}













private static function parseValue(string $value)
{
if (\trim($value) === '') {

return Success::create(Value::blank());
}

return \array_reduce(\iterator_to_array(Lexer::lex($value)), static function (Result $data, string $token) {
return $data->flatMap(static function (array $data) use ($token) {
return self::processToken($data[1], $token)->map(static function (array $val) use ($data) {
return [$data[0]->append($val[0], $val[1]), $val[2]];
});
});
}, Success::create([Value::blank(), self::INITIAL_STATE]))->flatMap(static function (array $result) {
/**
@psalm-suppress */
if (in_array($result[1], self::REJECT_STATES, true)) {

return Error::create('a missing closing quote');
}


return Success::create($result[0]);
})->mapError(static function (string $err) use ($value) {
return self::getErrorMessage($err, $value);
});
}









private static function processToken(int $state, string $token)
{
switch ($state) {
case self::INITIAL_STATE:
if ($token === '\'') {

return Success::create(['', false, self::SINGLE_QUOTED_STATE]);
} elseif ($token === '"') {

return Success::create(['', false, self::DOUBLE_QUOTED_STATE]);
} elseif ($token === '#') {

return Success::create(['', false, self::COMMENT_STATE]);
} elseif ($token === '$') {

return Success::create([$token, true, self::UNQUOTED_STATE]);
} else {

return Success::create([$token, false, self::UNQUOTED_STATE]);
}
case self::UNQUOTED_STATE:
if ($token === '#') {

return Success::create(['', false, self::COMMENT_STATE]);
} elseif (\ctype_space($token)) {

return Success::create(['', false, self::WHITESPACE_STATE]);
} elseif ($token === '$') {

return Success::create([$token, true, self::UNQUOTED_STATE]);
} else {

return Success::create([$token, false, self::UNQUOTED_STATE]);
}
case self::SINGLE_QUOTED_STATE:
if ($token === '\'') {

return Success::create(['', false, self::WHITESPACE_STATE]);
} else {

return Success::create([$token, false, self::SINGLE_QUOTED_STATE]);
}
case self::DOUBLE_QUOTED_STATE:
if ($token === '"') {

return Success::create(['', false, self::WHITESPACE_STATE]);
} elseif ($token === '\\') {

return Success::create(['', false, self::ESCAPE_SEQUENCE_STATE]);
} elseif ($token === '$') {

return Success::create([$token, true, self::DOUBLE_QUOTED_STATE]);
} else {

return Success::create([$token, false, self::DOUBLE_QUOTED_STATE]);
}
case self::ESCAPE_SEQUENCE_STATE:
if ($token === '"' || $token === '\\') {

return Success::create([$token, false, self::DOUBLE_QUOTED_STATE]);
} elseif ($token === '$') {

return Success::create([$token, false, self::DOUBLE_QUOTED_STATE]);
} else {
$first = Str::substr($token, 0, 1);
if (\in_array($first, ['f', 'n', 'r', 't', 'v'], true)) {

return Success::create([\stripcslashes('\\'.$first).Str::substr($token, 1), false, self::DOUBLE_QUOTED_STATE]);
} else {

return Error::create('an unexpected escape sequence');
}
}
case self::WHITESPACE_STATE:
if ($token === '#') {

return Success::create(['', false, self::COMMENT_STATE]);
} elseif (!\ctype_space($token)) {

return Error::create('unexpected whitespace');
} else {

return Success::create(['', false, self::WHITESPACE_STATE]);
}
case self::COMMENT_STATE:

return Success::create(['', false, self::COMMENT_STATE]);
default:
throw new \Error('Parser entered invalid state.');
}
}









private static function getErrorMessage(string $cause, string $subject)
{
return \sprintf(
'Encountered %s at [%s].',
$cause,
\strtok($subject, "\n")
);
}
}
