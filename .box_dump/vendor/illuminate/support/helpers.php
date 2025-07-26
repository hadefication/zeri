<?php

use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\Fluent;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Support\Once;
use Illuminate\Support\Onceable;
use Illuminate\Support\Optional;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable as SupportStringable;

if (! function_exists('append_config')) {






function append_config(array $array)
{
$start = 9999;

foreach ($array as $key => $value) {
if (is_numeric($key)) {
$start++;

$array[$start] = Arr::pull($array, $key);
}
}

return $array;
}
}

if (! function_exists('blank')) {
/**
@phpstan-assert-if-false
@phpstan-assert-if-true






*/
function blank($value)
{
if (is_null($value)) {
return true;
}

if (is_string($value)) {
return trim($value) === '';
}

if (is_numeric($value) || is_bool($value)) {
return false;
}

if ($value instanceof Model) {
return false;
}

if ($value instanceof Countable) {
return count($value) === 0;
}

if ($value instanceof Stringable) {
return trim((string) $value) === '';
}

return empty($value);
}
}

if (! function_exists('class_basename')) {






function class_basename($class)
{
$class = is_object($class) ? get_class($class) : $class;

return basename(str_replace('\\', '/', $class));
}
}

if (! function_exists('class_uses_recursive')) {






function class_uses_recursive($class)
{
if (is_object($class)) {
$class = get_class($class);
}

$results = [];

foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $class) {
$results += trait_uses_recursive($class);
}

return array_unique($results);
}
}

if (! function_exists('e')) {







function e($value, $doubleEncode = true)
{
if ($value instanceof DeferringDisplayableValue) {
$value = $value->resolveDisplayableValue();
}

if ($value instanceof Htmlable) {
return $value->toHtml();
}

if ($value instanceof BackedEnum) {
$value = $value->value;
}

return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
}
}

if (! function_exists('env')) {







function env($key, $default = null)
{
return Env::get($key, $default);
}
}

if (! function_exists('filled')) {
/**
@phpstan-assert-if-true
@phpstan-assert-if-false






*/
function filled($value)
{
return ! blank($value);
}
}

if (! function_exists('fluent')) {






function fluent($value)
{
return new Fluent($value);
}
}

if (! function_exists('literal')) {





function literal(...$arguments)
{
if (count($arguments) === 1 && array_is_list($arguments)) {
return $arguments[0];
}

return (object) $arguments;
}
}

if (! function_exists('object_get')) {
/**
@template







*/
function object_get($object, $key, $default = null)
{
if (is_null($key) || trim($key) === '') {
return $object;
}

foreach (explode('.', $key) as $segment) {
if (! is_object($object) || ! isset($object->{$segment})) {
return value($default);
}

$object = $object->{$segment};
}

return $object;
}
}

if (! function_exists('laravel_cloud')) {





function laravel_cloud()
{
return ($_ENV['LARAVEL_CLOUD'] ?? false) === '1' ||
($_SERVER['LARAVEL_CLOUD'] ?? false) === '1';
}
}

if (! function_exists('once')) {
/**
@template





*/
function once(callable $callback)
{
$onceable = Onceable::tryFromTrace(
debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2),
$callback,
);

return $onceable ? Once::instance()->value($onceable) : call_user_func($callback);
}
}

if (! function_exists('optional')) {
/**
@template
@template






*/
function optional($value = null, ?callable $callback = null)
{
if (is_null($callback)) {
return new Optional($value);
} elseif (! is_null($value)) {
return $callback($value);
}
}
}

if (! function_exists('preg_replace_array')) {








function preg_replace_array($pattern, array $replacements, $subject)
{
return preg_replace_callback($pattern, function () use (&$replacements) {
foreach ($replacements as $value) {
return array_shift($replacements);
}
}, $subject);
}
}

if (! function_exists('retry')) {
/**
@template










*/
function retry($times, callable $callback, $sleepMilliseconds = 0, $when = null)
{
$attempts = 0;

$backoff = [];

if (is_array($times)) {
$backoff = $times;

$times = count($times) + 1;
}

beginning:
$attempts++;
$times--;

try {
return $callback($attempts);
} catch (Throwable $e) {
if ($times < 1 || ($when && ! $when($e))) {
throw $e;
}

$sleepMilliseconds = $backoff[$attempts - 1] ?? $sleepMilliseconds;

if ($sleepMilliseconds) {
Sleep::usleep(value($sleepMilliseconds, $attempts, $e) * 1000);
}

goto beginning;
}
}
}

if (! function_exists('str')) {






function str($string = null)
{
if (func_num_args() === 0) {
return new class
{
public function __call($method, $parameters)
{
return Str::$method(...$parameters);
}

public function __toString()
{
return '';
}
};
}

return new SupportStringable($string);
}
}

if (! function_exists('tap')) {
/**
@template






*/
function tap($value, $callback = null)
{
if (is_null($callback)) {
return new HigherOrderTapProxy($value);
}

$callback($value);

return $value;
}
}

if (! function_exists('throw_if')) {
/**
@template
@template









*/
function throw_if($condition, $exception = 'RuntimeException', ...$parameters)
{
if ($condition) {
if (is_string($exception) && class_exists($exception)) {
$exception = new $exception(...$parameters);
}

throw is_string($exception) ? new RuntimeException($exception) : $exception;
}

return $condition;
}
}

if (! function_exists('throw_unless')) {
/**
@template
@template









*/
function throw_unless($condition, $exception = 'RuntimeException', ...$parameters)
{
throw_if(! $condition, $exception, ...$parameters);

return $condition;
}
}

if (! function_exists('trait_uses_recursive')) {






function trait_uses_recursive($trait)
{
$traits = class_uses($trait) ?: [];

foreach ($traits as $trait) {
$traits += trait_uses_recursive($trait);
}

return $traits;
}
}

if (! function_exists('transform')) {
/**
@template
@template
@template







*/
function transform($value, callable $callback, $default = null)
{
if (filled($value)) {
return $callback($value);
}

if (is_callable($default)) {
return $default($value);
}

return $default;
}
}

if (! function_exists('windows_os')) {





function windows_os()
{
return PHP_OS_FAMILY === 'Windows';
}
}

if (! function_exists('with')) {
/**
@template
@template






*/
function with($value, ?callable $callback = null)
{
return is_null($callback) ? $value : $callback($value);
}
}
