<?php

namespace Laravel\Prompts\Concerns;

use InvalidArgumentException;

trait Truncation
{



protected function truncate(string $string, int $width): string
{
if ($width <= 0) {
throw new InvalidArgumentException("Width [{$width}] must be greater than zero.");
}

return mb_strwidth($string) <= $width ? $string : (mb_strimwidth($string, 0, $width - 1).'…');
}






protected function mbWordwrap(
string $string,
int $width = 75,
string $break = "\n",
bool $cut_long_words = false
): string {
$lines = explode($break, $string);
$result = [];

foreach ($lines as $originalLine) {
if (mb_strwidth($originalLine) <= $width) {
$result[] = $originalLine;

continue;
}

$words = explode(' ', $originalLine);
$line = null;
$lineWidth = 0;

if ($cut_long_words) {
foreach ($words as $index => $word) {
$characters = mb_str_split($word);
$strings = [];
$str = '';

foreach ($characters as $character) {
$tmp = $str.$character;

if (mb_strwidth($tmp) > $width) {
$strings[] = $str;
$str = $character;
} else {
$str = $tmp;
}
}

if ($str !== '') {
$strings[] = $str;
}

$words[$index] = implode(' ', $strings);
}

$words = explode(' ', implode(' ', $words));
}

foreach ($words as $word) {
$tmp = ($line === null) ? $word : $line.' '.$word;


preg_match('/\p{Cf}/u', $word, $joinerMatches);

$wordWidth = count($joinerMatches) > 0 ? 2 : mb_strwidth($word);

$lineWidth += $wordWidth;

if ($line !== null) {

$lineWidth += 1;
}

if ($lineWidth <= $width) {
$line = $tmp;
} else {
$result[] = $line;
$line = $word;
$lineWidth = $wordWidth;
}
}

if ($line !== '') {
$result[] = $line;
}

$line = null;
}

return implode($break, $result);
}
}
