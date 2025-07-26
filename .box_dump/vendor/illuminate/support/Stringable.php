<?php

namespace Illuminate\Support;

use ArrayAccess;
use Closure;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use JsonSerializable;
use Stringable as BaseStringable;

class Stringable implements JsonSerializable, ArrayAccess, BaseStringable
{
use Conditionable, Dumpable, Macroable, Tappable;






protected $value;






public function __construct($value = '')
{
$this->value = (string) $value;
}







public function after($search)
{
return new static(Str::after($this->value, $search));
}







public function afterLast($search)
{
return new static(Str::afterLast($this->value, $search));
}







public function append(...$values)
{
return new static($this->value.implode('', $values));
}







public function newLine($count = 1)
{
return $this->append(str_repeat(PHP_EOL, $count));
}







public function ascii($language = 'en')
{
return new static(Str::ascii($this->value, $language));
}







public function basename($suffix = '')
{
return new static(basename($this->value, $suffix));
}







public function charAt($index)
{
return Str::charAt($this->value, $index);
}







public function chopStart($needle)
{
return new static(Str::chopStart($this->value, $needle));
}







public function chopEnd($needle)
{
return new static(Str::chopEnd($this->value, $needle));
}






public function classBasename()
{
return new static(class_basename($this->value));
}







public function before($search)
{
return new static(Str::before($this->value, $search));
}







public function beforeLast($search)
{
return new static(Str::beforeLast($this->value, $search));
}








public function between($from, $to)
{
return new static(Str::between($this->value, $from, $to));
}








public function betweenFirst($from, $to)
{
return new static(Str::betweenFirst($this->value, $from, $to));
}






public function camel()
{
return new static(Str::camel($this->value));
}








public function contains($needles, $ignoreCase = false)
{
return Str::contains($this->value, $needles, $ignoreCase);
}








public function containsAll($needles, $ignoreCase = false)
{
return Str::containsAll($this->value, $needles, $ignoreCase);
}








public function convertCase(int $mode = MB_CASE_FOLD, ?string $encoding = 'UTF-8')
{
return new static(Str::convertCase($this->value, $mode, $encoding));
}







public function deduplicate(string $character = ' ')
{
return new static(Str::deduplicate($this->value, $character));
}







public function dirname($levels = 1)
{
return new static(dirname($this->value, $levels));
}







public function endsWith($needles)
{
return Str::endsWith($this->value, $needles);
}







public function doesntEndWith($needles)
{
return Str::doesntEndWith($this->value, $needles);
}







public function exactly($value)
{
if ($value instanceof Stringable) {
$value = $value->toString();
}

return $this->value === $value;
}








public function excerpt($phrase = '', $options = [])
{
return Str::excerpt($this->value, $phrase, $options);
}








public function explode($delimiter, $limit = PHP_INT_MAX)
{
return new Collection(explode($delimiter, $this->value, $limit));
}









public function split($pattern, $limit = -1, $flags = 0)
{
if (filter_var($pattern, FILTER_VALIDATE_INT) !== false) {
return new Collection(mb_str_split($this->value, $pattern));
}

$segments = preg_split($pattern, $this->value, $limit, $flags);

return ! empty($segments) ? new Collection($segments) : new Collection;
}







public function finish($cap)
{
return new static(Str::finish($this->value, $cap));
}








public function is($pattern, $ignoreCase = false)
{
return Str::is($pattern, $this->value, $ignoreCase);
}






public function isAscii()
{
return Str::isAscii($this->value);
}






public function isJson()
{
return Str::isJson($this->value);
}






public function isUrl()
{
return Str::isUrl($this->value);
}






public function isUuid()
{
return Str::isUuid($this->value);
}






public function isUlid()
{
return Str::isUlid($this->value);
}






public function isEmpty()
{
return $this->value === '';
}






public function isNotEmpty()
{
return ! $this->isEmpty();
}






public function kebab()
{
return new static(Str::kebab($this->value));
}







public function length($encoding = null)
{
return Str::length($this->value, $encoding);
}









public function limit($limit = 100, $end = '...', $preserveWords = false)
{
return new static(Str::limit($this->value, $limit, $end, $preserveWords));
}






public function lower()
{
return new static(Str::lower($this->value));
}








public function markdown(array $options = [], array $extensions = [])
{
return new static(Str::markdown($this->value, $options, $extensions));
}








public function inlineMarkdown(array $options = [], array $extensions = [])
{
return new static(Str::inlineMarkdown($this->value, $options, $extensions));
}










public function mask($character, $index, $length = null, $encoding = 'UTF-8')
{
return new static(Str::mask($this->value, $character, $index, $length, $encoding));
}







public function match($pattern)
{
return new static(Str::match($pattern, $this->value));
}







public function isMatch($pattern)
{
return Str::isMatch($pattern, $this->value);
}







public function matchAll($pattern)
{
return Str::matchAll($pattern, $this->value);
}







public function test($pattern)
{
return $this->isMatch($pattern);
}






public function numbers()
{
return new static(Str::numbers($this->value));
}








public function padBoth($length, $pad = ' ')
{
return new static(Str::padBoth($this->value, $length, $pad));
}








public function padLeft($length, $pad = ' ')
{
return new static(Str::padLeft($this->value, $length, $pad));
}








public function padRight($length, $pad = ' ')
{
return new static(Str::padRight($this->value, $length, $pad));
}







public function parseCallback($default = null)
{
return Str::parseCallback($this->value, $default);
}







public function pipe(callable $callback)
{
return new static($callback($this));
}







public function plural($count = 2)
{
return new static(Str::plural($this->value, $count));
}







public function pluralStudly($count = 2)
{
return new static(Str::pluralStudly($this->value, $count));
}







public function pluralPascal($count = 2)
{
return new static(Str::pluralStudly($this->value, $count));
}









public function position($needle, $offset = 0, $encoding = null)
{
return Str::position($this->value, $needle, $offset, $encoding);
}







public function prepend(...$values)
{
return new static(implode('', $values).$this->value);
}








public function remove($search, $caseSensitive = true)
{
return new static(Str::remove($search, $this->value, $caseSensitive));
}






public function reverse()
{
return new static(Str::reverse($this->value));
}







public function repeat(int $times)
{
return new static(str_repeat($this->value, $times));
}









public function replace($search, $replace, $caseSensitive = true)
{
return new static(Str::replace($search, $replace, $this->value, $caseSensitive));
}








public function replaceArray($search, $replace)
{
return new static(Str::replaceArray($search, $replace, $this->value));
}








public function replaceFirst($search, $replace)
{
return new static(Str::replaceFirst($search, $replace, $this->value));
}








public function replaceStart($search, $replace)
{
return new static(Str::replaceStart($search, $replace, $this->value));
}








public function replaceLast($search, $replace)
{
return new static(Str::replaceLast($search, $replace, $this->value));
}








public function replaceEnd($search, $replace)
{
return new static(Str::replaceEnd($search, $replace, $this->value));
}









public function replaceMatches($pattern, $replace, $limit = -1)
{
if ($replace instanceof Closure) {
return new static(preg_replace_callback($pattern, $replace, $this->value, $limit));
}

return new static(preg_replace($pattern, $replace, $this->value, $limit));
}







public function scan($format)
{
return new Collection(sscanf($this->value, $format));
}






public function squish()
{
return new static(Str::squish($this->value));
}







public function start($prefix)
{
return new static(Str::start($this->value, $prefix));
}







public function stripTags($allowedTags = null)
{
return new static(strip_tags($this->value, $allowedTags));
}






public function upper()
{
return new static(Str::upper($this->value));
}






public function title()
{
return new static(Str::title($this->value));
}






public function headline()
{
return new static(Str::headline($this->value));
}






public function apa()
{
return new static(Str::apa($this->value));
}








public function transliterate($unknown = '?', $strict = false)
{
return new static(Str::transliterate($this->value, $unknown, $strict));
}






public function singular()
{
return new static(Str::singular($this->value));
}









public function slug($separator = '-', $language = 'en', $dictionary = ['@' => 'at'])
{
return new static(Str::slug($this->value, $separator, $language, $dictionary));
}







public function snake($delimiter = '_')
{
return new static(Str::snake($this->value, $delimiter));
}







public function startsWith($needles)
{
return Str::startsWith($this->value, $needles);
}







public function doesntStartWith($needles)
{
return Str::doesntStartWith($this->value, $needles);
}






public function studly()
{
return new static(Str::studly($this->value));
}






public function pascal()
{
return new static(Str::pascal($this->value));
}









public function substr($start, $length = null, $encoding = 'UTF-8')
{
return new static(Str::substr($this->value, $start, $length, $encoding));
}









public function substrCount($needle, $offset = 0, $length = null)
{
return Str::substrCount($this->value, $needle, $offset, $length);
}









public function substrReplace($replace, $offset = 0, $length = null)
{
return new static(Str::substrReplace($this->value, $replace, $offset, $length));
}







public function swap(array $map)
{
return new static(strtr($this->value, $map));
}







public function take(int $limit)
{
if ($limit < 0) {
return $this->substr($limit);
}

return $this->substr(0, $limit);
}







public function trim($characters = null)
{
return new static(Str::trim(...array_merge([$this->value], func_get_args())));
}







public function ltrim($characters = null)
{
return new static(Str::ltrim(...array_merge([$this->value], func_get_args())));
}







public function rtrim($characters = null)
{
return new static(Str::rtrim(...array_merge([$this->value], func_get_args())));
}






public function lcfirst()
{
return new static(Str::lcfirst($this->value));
}






public function ucfirst()
{
return new static(Str::ucfirst($this->value));
}






public function ucsplit()
{
return new Collection(Str::ucsplit($this->value));
}









public function whenContains($needles, $callback, $default = null)
{
return $this->when($this->contains($needles), $callback, $default);
}









public function whenContainsAll(array $needles, $callback, $default = null)
{
return $this->when($this->containsAll($needles), $callback, $default);
}








public function whenEmpty($callback, $default = null)
{
return $this->when($this->isEmpty(), $callback, $default);
}








public function whenNotEmpty($callback, $default = null)
{
return $this->when($this->isNotEmpty(), $callback, $default);
}









public function whenEndsWith($needles, $callback, $default = null)
{
return $this->when($this->endsWith($needles), $callback, $default);
}









public function whenDoesntEndWith($needles, $callback, $default = null)
{
return $this->when($this->doesntEndWith($needles), $callback, $default);
}









public function whenExactly($value, $callback, $default = null)
{
return $this->when($this->exactly($value), $callback, $default);
}









public function whenNotExactly($value, $callback, $default = null)
{
return $this->when(! $this->exactly($value), $callback, $default);
}









public function whenIs($pattern, $callback, $default = null)
{
return $this->when($this->is($pattern), $callback, $default);
}








public function whenIsAscii($callback, $default = null)
{
return $this->when($this->isAscii(), $callback, $default);
}








public function whenIsUuid($callback, $default = null)
{
return $this->when($this->isUuid(), $callback, $default);
}








public function whenIsUlid($callback, $default = null)
{
return $this->when($this->isUlid(), $callback, $default);
}









public function whenStartsWith($needles, $callback, $default = null)
{
return $this->when($this->startsWith($needles), $callback, $default);
}









public function whenDoesntStartWith($needles, $callback, $default = null)
{
return $this->when($this->doesntStartWith($needles), $callback, $default);
}









public function whenTest($pattern, $callback, $default = null)
{
return $this->when($this->test($pattern), $callback, $default);
}








public function words($words = 100, $end = '...')
{
return new static(Str::words($this->value, $words, $end));
}







public function wordCount($characters = null)
{
return Str::wordCount($this->value, $characters);
}









public function wordWrap($characters = 75, $break = "\n", $cutLongWords = false)
{
return new static(Str::wordWrap($this->value, $characters, $break, $cutLongWords));
}








public function wrap($before, $after = null)
{
return new static(Str::wrap($this->value, $before, $after));
}








public function unwrap($before, $after = null)
{
return new static(Str::unwrap($this->value, $before, $after));
}






public function toHtmlString()
{
return new HtmlString($this->value);
}






public function toBase64()
{
return new static(base64_encode($this->value));
}







public function fromBase64($strict = false)
{
return new static(base64_decode($this->value, $strict));
}







public function hash(string $algorithm)
{
return new static(hash($algorithm, $this->value));
}







public function encrypt(bool $serialize = false)
{
return new static(encrypt($this->value, $serialize));
}







public function decrypt(bool $serialize = false)
{
return new static(decrypt($this->value, $serialize));
}







public function dump(...$args)
{
dump($this->value, ...$args);

return $this;
}






public function value()
{
return $this->toString();
}






public function toString()
{
return $this->value;
}







public function toInteger($base = 10)
{
return intval($this->value, $base);
}






public function toFloat()
{
return floatval($this->value);
}








public function toBoolean()
{
return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
}










public function toDate($format = null, $tz = null)
{
if (is_null($format)) {
return Date::parse($this->value, $tz);
}

return Date::createFromFormat($format, $this->value, $tz);
}






public function toUri()
{
return Uri::of($this->value);
}






public function jsonSerialize(): string
{
return $this->__toString();
}







public function offsetExists(mixed $offset): bool
{
return isset($this->value[$offset]);
}







public function offsetGet(mixed $offset): string
{
return $this->value[$offset];
}







public function offsetSet(mixed $offset, mixed $value): void
{
$this->value[$offset] = $value;
}







public function offsetUnset(mixed $offset): void
{
unset($this->value[$offset]);
}







public function __get($key)
{
return $this->{$key}();
}






public function __toString()
{
return (string) $this->value;
}
}
