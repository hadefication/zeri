<?php

namespace Illuminate\Support\Facades;

use Illuminate\Filesystem\Filesystem;


















































































class Storage extends Facade
{







public static function fake($disk = null, array $config = [])
{
$root = self::getRootPath($disk = $disk ?: static::$app['config']->get('filesystems.default'));

if ($token = ParallelTesting::token()) {
$root = "{$root}_test_{$token}";
}

(new Filesystem)->cleanDirectory($root);

static::set($disk, $fake = static::createLocalDriver(
self::buildDiskConfiguration($disk, $config, root: $root)
));

return tap($fake)->buildTemporaryUrlsUsing(function ($path, $expiration) {
return URL::to($path.'?expiration='.$expiration->getTimestamp());
});
}








public static function persistentFake($disk = null, array $config = [])
{
$disk = $disk ?: static::$app['config']->get('filesystems.default');

static::set($disk, $fake = static::createLocalDriver(
self::buildDiskConfiguration($disk, $config, root: self::getRootPath($disk))
));

return $fake;
}







protected static function getRootPath(string $disk): string
{
return storage_path('framework/testing/disks/'.$disk);
}









protected static function buildDiskConfiguration(string $disk, array $config, string $root): array
{
$originalConfig = static::$app['config']["filesystems.disks.{$disk}"] ?? [];

return array_merge([
'throw' => $originalConfig['throw'] ?? false],
$config,
['root' => $root]
);
}






protected static function getFacadeAccessor()
{
return 'filesystem';
}
}
