<?php










namespace Symfony\Component\Translation\Loader;






class PhpFileLoader extends FileLoader
{
private static ?array $cache = [];

protected function loadResource(string $resource): array
{
if ([] === self::$cache && \function_exists('opcache_invalidate') && filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOL) && (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) || filter_var(\ini_get('opcache.enable_cli'), \FILTER_VALIDATE_BOOL))) {
self::$cache = null;
}

if (null === self::$cache) {
return require $resource;
}

return self::$cache[$resource] ??= require $resource;
}
}
