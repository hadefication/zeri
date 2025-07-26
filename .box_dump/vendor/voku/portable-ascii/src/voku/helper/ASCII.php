<?php

declare(strict_types=1);

namespace voku\helper;

/**
@psalm-immutable
















*/
final class ASCII
{




const UZBEK_LANGUAGE_CODE = 'uz';

const TURKMEN_LANGUAGE_CODE = 'tk';

const THAI_LANGUAGE_CODE = 'th';

const PASHTO_LANGUAGE_CODE = 'ps';

const ORIYA_LANGUAGE_CODE = 'or';

const MONGOLIAN_LANGUAGE_CODE = 'mn';

const KOREAN_LANGUAGE_CODE = 'ko';

const KIRGHIZ_LANGUAGE_CODE = 'ky';

const ARMENIAN_LANGUAGE_CODE = 'hy';

const BENGALI_LANGUAGE_CODE = 'bn';

const BELARUSIAN_LANGUAGE_CODE = 'be';

const AMHARIC_LANGUAGE_CODE = 'am';

const JAPANESE_LANGUAGE_CODE = 'ja';

const CHINESE_LANGUAGE_CODE = 'zh';

const DUTCH_LANGUAGE_CODE = 'nl';

const ITALIAN_LANGUAGE_CODE = 'it';

const MACEDONIAN_LANGUAGE_CODE = 'mk';

const PORTUGUESE_LANGUAGE_CODE = 'pt';

const GREEKLISH_LANGUAGE_CODE = 'el__greeklish';

const GREEK_LANGUAGE_CODE = 'el';

const HINDI_LANGUAGE_CODE = 'hi';

const SWEDISH_LANGUAGE_CODE = 'sv';

const TURKISH_LANGUAGE_CODE = 'tr';

const BULGARIAN_LANGUAGE_CODE = 'bg';

const HUNGARIAN_LANGUAGE_CODE = 'hu';

const MYANMAR_LANGUAGE_CODE = 'my';

const CROATIAN_LANGUAGE_CODE = 'hr';

const FINNISH_LANGUAGE_CODE = 'fi';

const GEORGIAN_LANGUAGE_CODE = 'ka';

const RUSSIAN_LANGUAGE_CODE = 'ru';

const RUSSIAN_PASSPORT_2013_LANGUAGE_CODE = 'ru__passport_2013';

const RUSSIAN_GOST_2000_B_LANGUAGE_CODE = 'ru__gost_2000_b';

const UKRAINIAN_LANGUAGE_CODE = 'uk';

const KAZAKH_LANGUAGE_CODE = 'kk';

const CZECH_LANGUAGE_CODE = 'cs';

const DANISH_LANGUAGE_CODE = 'da';

const POLISH_LANGUAGE_CODE = 'pl';

const ROMANIAN_LANGUAGE_CODE = 'ro';

const ESPERANTO_LANGUAGE_CODE = 'eo';

const ESTONIAN_LANGUAGE_CODE = 'et';

const LATVIAN_LANGUAGE_CODE = 'lv';

const LITHUANIAN_LANGUAGE_CODE = 'lt';

const NORWEGIAN_LANGUAGE_CODE = 'no';

const VIETNAMESE_LANGUAGE_CODE = 'vi';

const ARABIC_LANGUAGE_CODE = 'ar';

const PERSIAN_LANGUAGE_CODE = 'fa';

const SERBIAN_LANGUAGE_CODE = 'sr';

const SERBIAN_CYRILLIC_LANGUAGE_CODE = 'sr__cyr';

const SERBIAN_LATIN_LANGUAGE_CODE = 'sr__lat';

const AZERBAIJANI_LANGUAGE_CODE = 'az';

const SLOVAK_LANGUAGE_CODE = 'sk';

const FRENCH_LANGUAGE_CODE = 'fr';

const FRENCH_AUSTRIAN_LANGUAGE_CODE = 'fr_at';

const FRENCH_SWITZERLAND_LANGUAGE_CODE = 'fr_ch';

const GERMAN_LANGUAGE_CODE = 'de';

const GERMAN_AUSTRIAN_LANGUAGE_CODE = 'de_at';

const GERMAN_SWITZERLAND_LANGUAGE_CODE = 'de_ch';

const ENGLISH_LANGUAGE_CODE = 'en';

const EXTRA_LATIN_CHARS_LANGUAGE_CODE = 'latin';

const EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE = ' ';

const EXTRA_MSWORD_CHARS_LANGUAGE_CODE = 'msword';




private static $ASCII_MAPS;




private static $ASCII_MAPS_AND_EXTRAS;




private static $ASCII_EXTRAS;




private static $ORD;




private static $LANGUAGE_MAX_KEY;






private static $REGEX_ASCII = "[^\x09\x10\x13\x0A\x0D\x20-\x7E]";








private static $BIDI_UNI_CODE_CONTROLS_TABLE = [

8234 => "\xE2\x80\xAA",

8235 => "\xE2\x80\xAB",

8236 => "\xE2\x80\xAC",

8237 => "\xE2\x80\xAD",

8238 => "\xE2\x80\xAE",

8294 => "\xE2\x81\xA6",

8295 => "\xE2\x81\xA7",

8296 => "\xE2\x81\xA8",

8297 => "\xE2\x81\xA9",
];








public static function getAllLanguages(): array
{

static $LANGUAGES = [];

if ($LANGUAGES !== []) {
return $LANGUAGES;
}

foreach ((new \ReflectionClass(__CLASS__))->getConstants() as $constant => $lang) {
if (\strpos($constant, 'EXTRA') !== false) {
$LANGUAGES[\strtolower($constant)] = $lang;
} else {
$LANGUAGES[\strtolower(\str_replace('_LANGUAGE_CODE', '', $constant))] = $lang;
}
}

return $LANGUAGES;
}

/**
@psalm-pure













*/
public static function charsArray(bool $replace_extra_symbols = false): array
{
if ($replace_extra_symbols) {
self::prepareAsciiAndExtrasMaps();

return self::$ASCII_MAPS_AND_EXTRAS ?? [];
}

self::prepareAsciiMaps();

return self::$ASCII_MAPS ?? [];
}

/**
@psalm-pure












*/
public static function charsArrayWithMultiLanguageValues(bool $replace_extra_symbols = false): array
{
static $CHARS_ARRAY = [];
$cacheKey = '' . $replace_extra_symbols;

if (isset($CHARS_ARRAY[$cacheKey])) {
return $CHARS_ARRAY[$cacheKey];
}


$return = [];
$language_all_chars = self::charsArrayWithSingleLanguageValues(
$replace_extra_symbols,
false
);


foreach ($language_all_chars as $key => &$value) {
$return[$value][] = $key;
}

$CHARS_ARRAY[$cacheKey] = $return;

return $return;
}

/**
@psalm-pure
@phpstan-param




















*/
public static function charsArrayWithOneLanguage(
string $language = self::ENGLISH_LANGUAGE_CODE,
bool $replace_extra_symbols = false,
bool $asOrigReplaceArray = true
): array {
$language = self::get_language($language);


static $CHARS_ARRAY = [];
$cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;


if (isset($CHARS_ARRAY[$cacheKey][$language])) {
return $CHARS_ARRAY[$cacheKey][$language];
}

if ($replace_extra_symbols) {
self::prepareAsciiAndExtrasMaps();

if (isset(self::$ASCII_MAPS_AND_EXTRAS[$language])) {
$tmpArray = self::$ASCII_MAPS_AND_EXTRAS[$language];

if ($asOrigReplaceArray) {
$CHARS_ARRAY[$cacheKey][$language] = [
'orig' => \array_keys($tmpArray),
'replace' => \array_values($tmpArray),
];
} else {
$CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
}
} else {
if ($asOrigReplaceArray) {
$CHARS_ARRAY[$cacheKey][$language] = [
'orig' => [],
'replace' => [],
];
} else {
$CHARS_ARRAY[$cacheKey][$language] = [];
}
}
} else {
self::prepareAsciiMaps();

if (isset(self::$ASCII_MAPS[$language])) {
$tmpArray = self::$ASCII_MAPS[$language];

if ($asOrigReplaceArray) {
$CHARS_ARRAY[$cacheKey][$language] = [
'orig' => \array_keys($tmpArray),
'replace' => \array_values($tmpArray),
];
} else {
$CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
}
} else {
if ($asOrigReplaceArray) {
$CHARS_ARRAY[$cacheKey][$language] = [
'orig' => [],
'replace' => [],
];
} else {
$CHARS_ARRAY[$cacheKey][$language] = [];
}
}
}

return $CHARS_ARRAY[$cacheKey][$language] ?? ['orig' => [], 'replace' => []];
}

/**
@psalm-pure














*/
public static function charsArrayWithSingleLanguageValues(
bool $replace_extra_symbols = false,
bool $asOrigReplaceArray = true
): array {

static $CHARS_ARRAY = [];
$cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;

if (isset($CHARS_ARRAY[$cacheKey])) {
return $CHARS_ARRAY[$cacheKey];
}

if ($replace_extra_symbols) {
self::prepareAsciiAndExtrasMaps();


foreach (self::$ASCII_MAPS_AND_EXTRAS ?? [] as &$map) {
$CHARS_ARRAY[$cacheKey][] = $map;
}
} else {
self::prepareAsciiMaps();


foreach (self::$ASCII_MAPS ?? [] as &$map) {
$CHARS_ARRAY[$cacheKey][] = $map;
}
}

$CHARS_ARRAY[$cacheKey] = \array_merge([], ...$CHARS_ARRAY[$cacheKey]);

if ($asOrigReplaceArray) {
$CHARS_ARRAY[$cacheKey] = [
'orig' => \array_keys($CHARS_ARRAY[$cacheKey]),
'replace' => \array_values($CHARS_ARRAY[$cacheKey]),
];
}

return $CHARS_ARRAY[$cacheKey];
}

/**
@psalm-pure

















*/
public static function clean(
string $str,
bool $normalize_whitespace = true,
bool $keep_non_breaking_space = false,
bool $normalize_msword = true,
bool $remove_invisible_characters = true
): string {



$regex = '/
          (
            (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
            |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
            |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
            |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
            ){1,100}                      # ...one or more times
          )
        | ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
        | ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
        /x';
$str = (string) \preg_replace($regex, '$1', $str);

if ($normalize_whitespace) {
$str = self::normalize_whitespace($str, $keep_non_breaking_space);
}

if ($normalize_msword) {
$str = self::normalize_msword($str);
}

if ($remove_invisible_characters) {
$str = self::remove_invisible_characters($str);
}

return $str;
}

/**
@psalm-pure














*/
public static function is_ascii(string $str): bool
{
if ($str === '') {
return true;
}

return !\preg_match('/' . self::$REGEX_ASCII . '/', $str);
}

/**
@psalm-pure













*/
public static function normalize_msword(string $str): string
{
if ($str === '') {
return '';
}

static $MSWORD_CACHE = ['orig' => [], 'replace' => []];

if (empty($MSWORD_CACHE['orig'])) {
self::prepareAsciiMaps();

$map = self::$ASCII_MAPS[self::EXTRA_MSWORD_CHARS_LANGUAGE_CODE] ?? [];

$MSWORD_CACHE = [
'orig' => \array_keys($map),
'replace' => \array_values($map),
];
}

return \str_replace($MSWORD_CACHE['orig'], $MSWORD_CACHE['replace'], $str);
}

/**
@psalm-pure















*/
public static function normalize_whitespace(
string $str,
bool $keepNonBreakingSpace = false,
bool $keepBidiUnicodeControls = false,
bool $normalize_control_characters = false
): string {
if ($str === '') {
return '';
}

static $WHITESPACE_CACHE = [];
$cacheKey = (int) $keepNonBreakingSpace;

if ($normalize_control_characters) {
$str = \str_replace(
[
"\x0d\x0c", 
"\xe2\x80\xa8", 
"\xe2\x80\xa9", 
"\x0c", 
"\x0b", 
],
[
"\n",
"\n",
"\n",
"\n",
"\t",
],
$str
);
}

if (!isset($WHITESPACE_CACHE[$cacheKey])) {
self::prepareAsciiMaps();

$WHITESPACE_CACHE[$cacheKey] = self::$ASCII_MAPS[self::EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE] ?? [];

if ($keepNonBreakingSpace) {
unset($WHITESPACE_CACHE[$cacheKey]["\xc2\xa0"]);
}

$WHITESPACE_CACHE[$cacheKey] = array_keys($WHITESPACE_CACHE[$cacheKey]);
}

if (!$keepBidiUnicodeControls) {
static $BIDI_UNICODE_CONTROLS_CACHE = null;

if ($BIDI_UNICODE_CONTROLS_CACHE === null) {
$BIDI_UNICODE_CONTROLS_CACHE = self::$BIDI_UNI_CODE_CONTROLS_TABLE;
}

$str = \str_replace($BIDI_UNICODE_CONTROLS_CACHE, '', $str);
}

return \str_replace($WHITESPACE_CACHE[$cacheKey], ' ', $str);
}

/**
@psalm-pure













*/
public static function remove_invisible_characters(
string $str,
bool $url_encoded = false,
string $replacement = '',
bool $keep_basic_control_characters = true
): string {

$non_displayables = [];





if ($url_encoded) {
$non_displayables[] = '/%0[0-8bcefBCEF]/'; 
$non_displayables[] = '/%1[0-9a-fA-F]/'; 
}

if ($keep_basic_control_characters) {
$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; 
} else {
$str = self::normalize_whitespace($str, false, false, true);
$non_displayables[] = '/[^\P{C}\s]/u';
}

do {
$str = (string) \preg_replace($non_displayables, $replacement, $str, -1, $count);
} while ($count !== 0);

return $str;
}













public static function to_ascii_remap(string $str1, string $str2): array
{
$charMap = [];
$str1 = self::to_ascii_remap_intern($str1, $charMap);
$str2 = self::to_ascii_remap_intern($str2, $charMap);

return [$str1, $str2];
}

/**
@psalm-pure
@phpstan-param




























*/
public static function to_ascii(
string $str,
string $language = self::ENGLISH_LANGUAGE_CODE,
bool $remove_unsupported_chars = true,
bool $replace_extra_symbols = false,
bool $use_transliterate = false,
bool $replace_single_chars_only = false
): string {
if ($str === '') {
return '';
}

/**
@phpstan-var */
$language = self::get_language($language);

static $EXTRA_SYMBOLS_CACHE = null;

static $REPLACE_HELPER_CACHE = [];
$cacheKey = $language . '-' . $replace_extra_symbols;

if (!isset($REPLACE_HELPER_CACHE[$cacheKey])) {
$langAll = self::charsArrayWithSingleLanguageValues($replace_extra_symbols, false);

$langSpecific = self::charsArrayWithOneLanguage($language, $replace_extra_symbols, false);

if ($langSpecific === []) {
$REPLACE_HELPER_CACHE[$cacheKey] = $langAll;
} else {
$REPLACE_HELPER_CACHE[$cacheKey] = \array_merge([], $langAll, $langSpecific);
}
}

if (
$replace_extra_symbols
&&
$EXTRA_SYMBOLS_CACHE === null
) {
$EXTRA_SYMBOLS_CACHE = [];
foreach (self::$ASCII_EXTRAS ?? [] as $extrasDataTmp) {
foreach ($extrasDataTmp as $extrasDataKeyTmp => $extrasDataValueTmp) {
$EXTRA_SYMBOLS_CACHE[$extrasDataKeyTmp] = $extrasDataKeyTmp;
}
}
$EXTRA_SYMBOLS_CACHE = \implode('', $EXTRA_SYMBOLS_CACHE);
}

$charDone = [];
if (\preg_match_all('/' . self::$REGEX_ASCII . ($replace_extra_symbols ? '|[' . $EXTRA_SYMBOLS_CACHE . ']' : '') . '/u', $str, $matches)) {
if (!$replace_single_chars_only) {
if (self::$LANGUAGE_MAX_KEY === null) {
self::$LANGUAGE_MAX_KEY = self::getData('ascii_language_max_key');
}

$maxKeyLength = self::$LANGUAGE_MAX_KEY[$language] ?? 0;

if ($maxKeyLength >= 5) {
foreach ($matches[0] as $keyTmp => $char) {
if (isset($matches[0][$keyTmp + 4])) {
$fiveChars = $matches[0][$keyTmp + 0] . $matches[0][$keyTmp + 1] . $matches[0][$keyTmp + 2] . $matches[0][$keyTmp + 3] . $matches[0][$keyTmp + 4];
} else {
$fiveChars = null;
}
if (
$fiveChars
&&
!isset($charDone[$fiveChars])
&&
isset($REPLACE_HELPER_CACHE[$cacheKey][$fiveChars])
&&
\strpos($str, $fiveChars) !== false
) {



$charDone[$fiveChars] = true;
$str = \str_replace($fiveChars, $REPLACE_HELPER_CACHE[$cacheKey][$fiveChars], $str);



}
}
}

if ($maxKeyLength >= 4) {
foreach ($matches[0] as $keyTmp => $char) {
if (isset($matches[0][$keyTmp + 3])) {
$fourChars = $matches[0][$keyTmp + 0] . $matches[0][$keyTmp + 1] . $matches[0][$keyTmp + 2] . $matches[0][$keyTmp + 3];
} else {
$fourChars = null;
}
if (
$fourChars
&&
!isset($charDone[$fourChars])
&&
isset($REPLACE_HELPER_CACHE[$cacheKey][$fourChars])
&&
\strpos($str, $fourChars) !== false
) {



$charDone[$fourChars] = true;
$str = \str_replace($fourChars, $REPLACE_HELPER_CACHE[$cacheKey][$fourChars], $str);



}
}
}

foreach ($matches[0] as $keyTmp => $char) {
if (isset($matches[0][$keyTmp + 2])) {
$threeChars = $matches[0][$keyTmp + 0] . $matches[0][$keyTmp + 1] . $matches[0][$keyTmp + 2];
} else {
$threeChars = null;
}
if (
$threeChars
&&
!isset($charDone[$threeChars])
&&
isset($REPLACE_HELPER_CACHE[$cacheKey][$threeChars])
&&
\strpos($str, $threeChars) !== false
) {



$charDone[$threeChars] = true;
$str = \str_replace($threeChars, $REPLACE_HELPER_CACHE[$cacheKey][$threeChars], $str);



}
}

foreach ($matches[0] as $keyTmp => $char) {
if (isset($matches[0][$keyTmp + 1])) {
$twoChars = $matches[0][$keyTmp + 0] . $matches[0][$keyTmp + 1];
} else {
$twoChars = null;
}
if (
$twoChars
&&
!isset($charDone[$twoChars])
&&
isset($REPLACE_HELPER_CACHE[$cacheKey][$twoChars])
&&
\strpos($str, $twoChars) !== false
) {



$charDone[$twoChars] = true;
$str = \str_replace($twoChars, $REPLACE_HELPER_CACHE[$cacheKey][$twoChars], $str);



}
}
}

foreach ($matches[0] as $char) {
if (
!isset($charDone[$char])
&&
isset($REPLACE_HELPER_CACHE[$cacheKey][$char])
&&
\strpos($str, $char) !== false
) {



$charDone[$char] = true;
$str = \str_replace($char, $REPLACE_HELPER_CACHE[$cacheKey][$char], $str);



}
}
}

if (!isset(self::$ASCII_MAPS[$language])) {
$use_transliterate = true;
}

if ($use_transliterate) {
$str = self::to_transliterate($str, null, false);
}

if ($remove_unsupported_chars) {
$str = (string) \str_replace(["\n\r", "\n", "\r", "\t"], ' ', $str);
$str = (string) \preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
}

return $str;
}

/**
@psalm-pure














*/
public static function to_filename(
string $str,
bool $use_transliterate = true,
string $fallback_char = '-'
): string {
if ($use_transliterate) {
$str = self::to_transliterate($str, $fallback_char);
}

$fallback_char_escaped = \preg_quote($fallback_char, '/');

$str = (string) \preg_replace(
[
'/[^' . $fallback_char_escaped . '.\\-a-zA-Z\d\\s]/', 
'/\s+/u', 
'/[' . $fallback_char_escaped . ']+/u', 
],
[
'',
$fallback_char,
$fallback_char,
],
$str
);

return \trim($str, $fallback_char);
}

/**
@psalm-pure
@phpstan-param





















*/
public static function to_slugify(
string $str,
string $separator = '-',
string $language = self::ENGLISH_LANGUAGE_CODE,
array $replacements = [],
bool $replace_extra_symbols = false,
bool $use_str_to_lower = true,
bool $use_transliterate = false
): string {
if ($str === '') {
return '';
}

foreach ($replacements as $from => $to) {
$str = \str_replace($from, $to, $str);
}

$str = self::to_ascii(
$str,
$language,
false,
$replace_extra_symbols,
$use_transliterate
);

$str = \str_replace('@', $separator, $str);

$str = (string) \preg_replace(
'/[^a-zA-Z\\d\\s\\-_' . \preg_quote($separator, '/') . ']/',
'',
$str
);

if ($use_str_to_lower) {
$str = \strtolower($str);
}

$str = (string) \preg_replace('/^[\'\\s]+|[\'\\s]+$/', '', $str);
$str = (string) \preg_replace('/\\B([A-Z])/', '-\1', $str);
$str = (string) \preg_replace('/[\\-_\\s]+/', $separator, $str);

$l = \strlen($separator);
if ($l && \strpos($str, $separator) === 0) {
$str = (string) \substr($str, $l);
}

if (\substr($str, -$l) === $separator) {
$str = (string) \substr($str, 0, \strlen($str) - $l);
}

return $str;
}

/**
@psalm-pure
















*/
public static function to_transliterate(
string $str,
$unknown = '?',
bool $strict = false
): string {
static $UTF8_TO_TRANSLIT = null;

static $TRANSLITERATOR = null;

static $SUPPORT_INTL = null;

if ($str === '') {
return '';
}

if ($SUPPORT_INTL === null) {
$SUPPORT_INTL = \extension_loaded('intl');
}


$str_tmp = $str;
if (self::is_ascii($str)) {
return $str;
}

$str = self::clean($str);


if (
$str_tmp !== $str
&&
self::is_ascii($str)
) {
return $str;
}

if (
$strict
&&
$SUPPORT_INTL === true
) {
if (!isset($TRANSLITERATOR)) {

$TRANSLITERATOR = \transliterator_create('NFKC; [:Nonspacing Mark:] Remove; NFKC; Any-Latin; Latin-ASCII;');
}


$str_tmp = \transliterator_transliterate($TRANSLITERATOR, $str);

if ($str_tmp !== false) {

if (
$str_tmp !== $str
&&
self::is_ascii($str_tmp)
) {
return $str_tmp;
}

$str = $str_tmp;
}
}

if (self::$ORD === null) {
self::$ORD = self::getData('ascii_ord');
}

\preg_match_all('/.|[^\x00]$/us', $str, $array_tmp);
$chars = $array_tmp[0];
$ord = null;
$str_tmp = '';
foreach ($chars as &$c) {
$ordC0 = self::$ORD[$c[0]];

if ($ordC0 >= 0 && $ordC0 <= 127) {
$str_tmp .= $c;

continue;
}

$ordC1 = self::$ORD[$c[1]];


if ($ordC0 >= 192 && $ordC0 <= 223) {
$ord = ($ordC0 - 192) * 64 + ($ordC1 - 128);
}

if ($ordC0 >= 224) {
$ordC2 = self::$ORD[$c[2]];

if ($ordC0 <= 239) {
$ord = ($ordC0 - 224) * 4096 + ($ordC1 - 128) * 64 + ($ordC2 - 128);
}

if ($ordC0 >= 240) {
$ordC3 = self::$ORD[$c[3]];

if ($ordC0 <= 247) {
$ord = ($ordC0 - 240) * 262144 + ($ordC1 - 128) * 4096 + ($ordC2 - 128) * 64 + ($ordC3 - 128);
}



















}
}

if (
$ordC0 === 254
||
$ordC0 === 255
||
$ord === null
) {
$str_tmp .= $unknown ?? $c;

continue;
}

$bank = $ord >> 8;
if (!isset($UTF8_TO_TRANSLIT[$bank])) {
$UTF8_TO_TRANSLIT[$bank] = self::getDataIfExists(\sprintf('x%03x', $bank));
}

$new_char = $ord & 255;

if (isset($UTF8_TO_TRANSLIT[$bank][$new_char])) {











$new_char = $UTF8_TO_TRANSLIT[$bank][$new_char];


if ($unknown === null && $new_char === '') {

} elseif (
$new_char === '[?]'
||
$new_char === '[?] '
) {
$c = $unknown ?? $c;
} else {
$c = $new_char;
}
} else {










$c = $unknown ?? $c;
}

$str_tmp .= $c;
}

return $str_tmp;
}

/**
@phpstan-param




















*/
private static function to_ascii_remap_intern(string $str, array &$map): string
{

$matches = [];
if (!\preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)) {
return $str; 
}


$mapCount = \count($map);
foreach ($matches[0] as $mbc) {
if (!isset($map[$mbc])) {
$map[$mbc] = \chr(128 + $mapCount);
++$mapCount;
}
}


return \strtr($str, $map);
}











private static function get_language(string $language)
{
if ($language === '') {
return '';
}

if (
\strpos($language, '_') === false
&&
\strpos($language, '-') === false
) {
return \strtolower($language);
}

$language = \str_replace('-', '_', \strtolower($language));

$regex = '/(?<first>[a-z]+)_\g{first}/';

return (string) \preg_replace($regex, '$1', $language);
}






private static function getData(string $file)
{
return include __DIR__ . '/data/' . $file . '.php';
}






private static function getDataIfExists(string $file): array
{
$file = __DIR__ . '/data/' . $file . '.php';
if (\is_file($file)) {
return include $file;
}

return [];
}




private static function prepareAsciiAndExtrasMaps()
{
if (self::$ASCII_MAPS_AND_EXTRAS === null) {
self::prepareAsciiMaps();
self::prepareAsciiExtras();

self::$ASCII_MAPS_AND_EXTRAS = \array_merge_recursive(
self::$ASCII_MAPS ?? [],
self::$ASCII_EXTRAS ?? []
);
}
}




private static function prepareAsciiMaps()
{
if (self::$ASCII_MAPS === null) {
self::$ASCII_MAPS = self::getData('ascii_by_languages');
}
}




private static function prepareAsciiExtras()
{
if (self::$ASCII_EXTRAS === null) {
self::$ASCII_EXTRAS = self::getData('ascii_extras_by_languages');
}
}
}
