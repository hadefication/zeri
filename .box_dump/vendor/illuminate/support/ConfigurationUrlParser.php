<?php

namespace Illuminate\Support;

use InvalidArgumentException;

class ConfigurationUrlParser
{





protected static $driverAliases = [
'mssql' => 'sqlsrv',
'mysql2' => 'mysql', 
'postgres' => 'pgsql',
'postgresql' => 'pgsql',
'sqlite3' => 'sqlite',
'redis' => 'tcp',
'rediss' => 'tls',
];







public function parseConfiguration($config)
{
if (is_string($config)) {
$config = ['url' => $config];
}

$url = Arr::pull($config, 'url');

if (! $url) {
return $config;
}

$rawComponents = $this->parseUrl($url);

$decodedComponents = $this->parseStringsToNativeTypes(
array_map(rawurldecode(...), $rawComponents)
);

return array_merge(
$config,
$this->getPrimaryOptions($decodedComponents),
$this->getQueryOptions($rawComponents)
);
}







protected function getPrimaryOptions($url)
{
return array_filter([
'driver' => $this->getDriver($url),
'database' => $this->getDatabase($url),
'host' => $url['host'] ?? null,
'port' => $url['port'] ?? null,
'username' => $url['user'] ?? null,
'password' => $url['pass'] ?? null,
], fn ($value) => ! is_null($value));
}







protected function getDriver($url)
{
$alias = $url['scheme'] ?? null;

if (! $alias) {
return;
}

return static::$driverAliases[$alias] ?? $alias;
}







protected function getDatabase($url)
{
$path = $url['path'] ?? null;

return $path && $path !== '/' ? substr($path, 1) : null;
}







protected function getQueryOptions($url)
{
$queryString = $url['query'] ?? null;

if (! $queryString) {
return [];
}

$query = [];

parse_str($queryString, $query);

return $this->parseStringsToNativeTypes($query);
}









protected function parseUrl($url)
{
$url = preg_replace('#^(sqlite3?):///#', '$1://null/', $url);

$parsedUrl = parse_url($url);

if ($parsedUrl === false) {
throw new InvalidArgumentException('The database configuration URL is malformed.');
}

return $parsedUrl;
}







protected function parseStringsToNativeTypes($value)
{
if (is_array($value)) {
return array_map($this->parseStringsToNativeTypes(...), $value);
}

if (! is_string($value)) {
return $value;
}

$parsedValue = json_decode($value, true);

if (json_last_error() === JSON_ERROR_NONE) {
return $parsedValue;
}

return $value;
}






public static function getDriverAliases()
{
return static::$driverAliases;
}








public static function addDriverAlias($alias, $driver)
{
static::$driverAliases[$alias] = $driver;
}
}
