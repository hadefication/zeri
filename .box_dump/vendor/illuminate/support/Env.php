<?php

namespace Illuminate\Support;

use Closure;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PhpOption\Option;
use RuntimeException;

class Env
{





protected static $putenv = true;






protected static $repository;






protected static $customAdapters = [];






public static function enablePutenv()
{
static::$putenv = true;
static::$repository = null;
}






public static function disablePutenv()
{
static::$putenv = false;
static::$repository = null;
}




public static function extend(Closure $callback, ?string $name = null): void
{
if (! is_null($name)) {
static::$customAdapters[$name] = $callback;
} else {
static::$customAdapters[] = $callback;
}
}






public static function getRepository()
{
if (static::$repository === null) {
$builder = RepositoryBuilder::createWithDefaultAdapters();

if (static::$putenv) {
$builder = $builder->addAdapter(PutenvAdapter::class);
}

foreach (static::$customAdapters as $adapter) {
$builder = $builder->addAdapter($adapter());
}

static::$repository = $builder->immutable()->make();
}

return static::$repository;
}








public static function get($key, $default = null)
{
return self::getOption($key)->getOrCall(fn () => value($default));
}









public static function getOrFail($key)
{
return self::getOption($key)->getOrThrow(new RuntimeException("Environment variable [$key] has no value."));
}












public static function writeVariables(array $variables, string $pathToFile, bool $overwrite = false): void
{
$filesystem = new Filesystem;

if ($filesystem->missing($pathToFile)) {
throw new RuntimeException("The file [{$pathToFile}] does not exist.");
}

$lines = explode(PHP_EOL, $filesystem->get($pathToFile));

foreach ($variables as $key => $value) {
$lines = self::addVariableToEnvContents($key, $value, $lines, $overwrite);
}

$filesystem->put($pathToFile, implode(PHP_EOL, $lines));
}













public static function writeVariable(string $key, mixed $value, string $pathToFile, bool $overwrite = false): void
{
$filesystem = new Filesystem;

if ($filesystem->missing($pathToFile)) {
throw new RuntimeException("The file [{$pathToFile}] does not exist.");
}

$envContent = $filesystem->get($pathToFile);

$lines = explode(PHP_EOL, $envContent);
$lines = self::addVariableToEnvContents($key, $value, $lines, $overwrite);

$filesystem->put($pathToFile, implode(PHP_EOL, $lines));
}










protected static function addVariableToEnvContents(string $key, mixed $value, array $envLines, bool $overwrite): array
{
$prefix = explode('_', $key)[0].'_';
$lastPrefixIndex = -1;

$shouldQuote = preg_match('/^[a-zA-z0-9]+$/', $value) === 0;

$lineToAddVariations = [
$key.'='.(is_string($value) ? '"'.addslashes($value).'"' : $value),
$key.'='.(is_string($value) ? "'".addslashes($value)."'" : $value),
$key.'='.$value,
];

$lineToAdd = $shouldQuote ? $lineToAddVariations[0] : $lineToAddVariations[2];

if ($value === '') {
$lineToAdd = $key.'=';
}

foreach ($envLines as $index => $line) {
if (str_starts_with($line, $prefix)) {
$lastPrefixIndex = $index;
}

if (in_array($line, $lineToAddVariations)) {

return $envLines;
}

if ($line === $key.'=') {

$envLines[$index] = $lineToAdd;

return $envLines;
}

if (str_starts_with($line, $key.'=')) {
if (! $overwrite) {
return $envLines;
}

$envLines[$index] = $lineToAdd;

return $envLines;
}
}

if ($lastPrefixIndex === -1) {
if (count($envLines) && $envLines[count($envLines) - 1] !== '') {
$envLines[] = '';
}

return array_merge($envLines, [$lineToAdd]);
}

return array_merge(
array_slice($envLines, 0, $lastPrefixIndex + 1),
[$lineToAdd],
array_slice($envLines, $lastPrefixIndex + 1)
);
}







protected static function getOption($key)
{
return Option::fromValue(static::getRepository()->get($key))
->map(function ($value) {
switch (strtolower($value)) {
case 'true':
case '(true)':
return true;
case 'false':
case '(false)':
return false;
case 'empty':
case '(empty)':
return '';
case 'null':
case '(null)':
return;
}

if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
return $matches[2];
}

return $value;
});
}
}
