<?php

namespace Illuminate\Filesystem;

use Aws\S3\S3Client;
use Closure;
use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as S3Adapter;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter as AwsS3PortableVisibilityConverter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;

use function Illuminate\Support\enum_value;

/**
@mixin
@mixin
*/
class FilesystemManager implements FactoryContract
{





protected $app;






protected $disks = [];






protected $customCreators = [];






public function __construct($app)
{
$this->app = $app;
}







public function drive($name = null)
{
return $this->disk($name);
}







public function disk($name = null)
{
$name = enum_value($name) ?: $this->getDefaultDriver();

return $this->disks[$name] = $this->get($name);
}






public function cloud()
{
$name = $this->getDefaultCloudDriver();

return $this->disks[$name] = $this->get($name);
}







public function build($config)
{
return $this->resolve('ondemand', is_array($config) ? $config : [
'driver' => 'local',
'root' => $config,
]);
}







protected function get($name)
{
return $this->disks[$name] ?? $this->resolve($name);
}










protected function resolve($name, $config = null)
{
$config ??= $this->getConfig($name);

if (empty($config['driver'])) {
throw new InvalidArgumentException("Disk [{$name}] does not have a configured driver.");
}

$driver = $config['driver'];

if (isset($this->customCreators[$driver])) {
return $this->callCustomCreator($config);
}

$driverMethod = 'create'.ucfirst($driver).'Driver';

if (! method_exists($this, $driverMethod)) {
throw new InvalidArgumentException("Driver [{$driver}] is not supported.");
}

return $this->{$driverMethod}($config, $name);
}







protected function callCustomCreator(array $config)
{
return $this->customCreators[$config['driver']]($this->app, $config);
}








public function createLocalDriver(array $config, string $name = 'local')
{
$visibility = PortableVisibilityConverter::fromArray(
$config['permissions'] ?? [],
$config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
);

$links = ($config['links'] ?? null) === 'skip'
? LocalAdapter::SKIP_LINKS
: LocalAdapter::DISALLOW_LINKS;

$adapter = new LocalAdapter(
$config['root'], $visibility, $config['lock'] ?? LOCK_EX, $links
);

return (new LocalFilesystemAdapter(
$this->createFlysystem($adapter, $config), $adapter, $config
))->diskName(
$name
)->shouldServeSignedUrls(
$config['serve'] ?? false,
fn () => $this->app['url'],
);
}







public function createFtpDriver(array $config)
{
if (! isset($config['root'])) {
$config['root'] = '';
}

$adapter = new FtpAdapter(FtpConnectionOptions::fromArray($config));

return new FilesystemAdapter($this->createFlysystem($adapter, $config), $adapter, $config);
}







public function createSftpDriver(array $config)
{
$provider = SftpConnectionProvider::fromArray($config);

$root = $config['root'] ?? '';

$visibility = PortableVisibilityConverter::fromArray(
$config['permissions'] ?? []
);

$adapter = new SftpAdapter($provider, $root, $visibility);

return new FilesystemAdapter($this->createFlysystem($adapter, $config), $adapter, $config);
}







public function createS3Driver(array $config)
{
$s3Config = $this->formatS3Config($config);

$root = (string) ($s3Config['root'] ?? '');

$visibility = new AwsS3PortableVisibilityConverter(
$config['visibility'] ?? Visibility::PUBLIC
);

$streamReads = $s3Config['stream_reads'] ?? false;

$client = new S3Client($s3Config);

$adapter = new S3Adapter($client, $s3Config['bucket'], $root, $visibility, null, $config['options'] ?? [], $streamReads);

return new AwsS3V3Adapter(
$this->createFlysystem($adapter, $config), $adapter, $s3Config, $client
);
}







protected function formatS3Config(array $config)
{
$config += ['version' => 'latest'];

if (! empty($config['key']) && ! empty($config['secret'])) {
$config['credentials'] = Arr::only($config, ['key', 'secret']);

if (! empty($config['token'])) {
$config['credentials']['token'] = $config['token'];
}
}

return Arr::except($config, ['token']);
}







public function createScopedDriver(array $config)
{
if (empty($config['disk'])) {
throw new InvalidArgumentException('Scoped disk is missing "disk" configuration option.');
} elseif (empty($config['prefix'])) {
throw new InvalidArgumentException('Scoped disk is missing "prefix" configuration option.');
}

return $this->build(tap(
is_string($config['disk']) ? $this->getConfig($config['disk']) : $config['disk'],
function (&$parent) use ($config) {
if (empty($parent['prefix'])) {
$parent['prefix'] = $config['prefix'];
} else {
$separator = $parent['directory_separator'] ?? DIRECTORY_SEPARATOR;

$parentPrefix = rtrim($parent['prefix'], $separator);
$scopedPrefix = ltrim($config['prefix'], $separator);

$parent['prefix'] = "{$parentPrefix}{$separator}{$scopedPrefix}";
}

if (isset($config['visibility'])) {
$parent['visibility'] = $config['visibility'];
}
}
));
}








protected function createFlysystem(FlysystemAdapter $adapter, array $config)
{
if ($config['read-only'] ?? false === true) {
$adapter = new ReadOnlyFilesystemAdapter($adapter);
}

if (! empty($config['prefix'])) {
$adapter = new PathPrefixedAdapter($adapter, $config['prefix']);
}

if (str_contains($config['endpoint'] ?? '', 'r2.cloudflarestorage.com')) {
$config['retain_visibility'] = false;
}

return new Flysystem($adapter, Arr::only($config, [
'directory_visibility',
'disable_asserts',
'retain_visibility',
'temporary_url',
'url',
'visibility',
]));
}








public function set($name, $disk)
{
$this->disks[$name] = $disk;

return $this;
}







protected function getConfig($name)
{
return $this->app['config']["filesystems.disks.{$name}"] ?: [];
}






public function getDefaultDriver()
{
return $this->app['config']['filesystems.default'];
}






public function getDefaultCloudDriver()
{
return $this->app['config']['filesystems.cloud'] ?? 's3';
}







public function forgetDisk($disk)
{
foreach ((array) $disk as $diskName) {
unset($this->disks[$diskName]);
}

return $this;
}







public function purge($name = null)
{
$name ??= $this->getDefaultDriver();

unset($this->disks[$name]);
}








public function extend($driver, Closure $callback)
{
$this->customCreators[$driver] = $callback;

return $this;
}







public function setApplication($app)
{
$this->app = $app;

return $this;
}








public function __call($method, $parameters)
{
return $this->disk()->$method(...$parameters);
}
}
