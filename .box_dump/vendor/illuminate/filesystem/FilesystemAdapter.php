<?php

namespace Illuminate\Filesystem;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Filesystem\Cloud as CloudFilesystemContract;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use League\Flysystem\FilesystemAdapter as FlysystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToProvideChecksum;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use PHPUnit\Framework\Assert as PHPUnit;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
@mixin
*/
class FilesystemAdapter implements CloudFilesystemContract
{
use Conditionable;
use Macroable {
__call as macroCall;
}






protected $driver;






protected $adapter;






protected $config;






protected $prefixer;






protected $serveCallback;






protected $temporaryUrlCallback;








public function __construct(FilesystemOperator $driver, FlysystemAdapter $adapter, array $config = [])
{
$this->driver = $driver;
$this->adapter = $adapter;
$this->config = $config;
$separator = $config['directory_separator'] ?? DIRECTORY_SEPARATOR;

$this->prefixer = new PathPrefixer($config['root'] ?? '', $separator);

if (isset($config['prefix'])) {
$this->prefixer = new PathPrefixer($this->prefixer->prefixPath($config['prefix']), $separator);
}
}








public function assertExists($path, $content = null)
{
clearstatcache();

$paths = Arr::wrap($path);

foreach ($paths as $path) {
PHPUnit::assertTrue(
$this->exists($path), "Unable to find a file or directory at path [{$path}]."
);

if (! is_null($content)) {
$actual = $this->get($path);

PHPUnit::assertSame(
$content,
$actual,
"File or directory [{$path}] was found, but content [{$actual}] does not match [{$content}]."
);
}
}

return $this;
}









public function assertCount($path, $count, $recursive = false)
{
clearstatcache();

$actual = count($this->files($path, $recursive));

PHPUnit::assertEquals(
$actual, $count, "Expected [{$count}] files at [{$path}], but found [{$actual}]."
);

return $this;
}







public function assertMissing($path)
{
clearstatcache();

$paths = Arr::wrap($path);

foreach ($paths as $path) {
PHPUnit::assertFalse(
$this->exists($path), "Found unexpected file or directory at path [{$path}]."
);
}

return $this;
}







public function assertDirectoryEmpty($path)
{
PHPUnit::assertEmpty(
$this->allFiles($path), "Directory [{$path}] is not empty."
);

return $this;
}







public function exists($path)
{
return $this->driver->has($path);
}







public function missing($path)
{
return ! $this->exists($path);
}







public function fileExists($path)
{
return $this->driver->fileExists($path);
}







public function fileMissing($path)
{
return ! $this->fileExists($path);
}







public function directoryExists($path)
{
return $this->driver->directoryExists($path);
}







public function directoryMissing($path)
{
return ! $this->directoryExists($path);
}







public function path($path)
{
return $this->prefixer->prefixPath($path);
}







public function get($path)
{
try {
return $this->driver->read($path);
} catch (UnableToReadFile $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);
}
}








public function json($path, $flags = 0)
{
$content = $this->get($path);

return is_null($content) ? null : json_decode($content, true, 512, $flags);
}










public function response($path, $name = null, array $headers = [], $disposition = 'inline')
{
$response = new StreamedResponse;

$headers['Content-Type'] ??= $this->mimeType($path);
$headers['Content-Length'] ??= $this->size($path);

if (! array_key_exists('Content-Disposition', $headers)) {
$filename = $name ?? basename($path);

$disposition = $response->headers->makeDisposition(
$disposition, $filename, $this->fallbackName($filename)
);

$headers['Content-Disposition'] = $disposition;
}

$response->headers->replace($headers);

$response->setCallback(function () use ($path) {
$stream = $this->readStream($path);
fpassthru($stream);
fclose($stream);
});

return $response;
}










public function serve(Request $request, $path, $name = null, array $headers = [])
{
return isset($this->serveCallback)
? call_user_func($this->serveCallback, $request, $path, $headers)
: $this->response($path, $name, $headers);
}









public function download($path, $name = null, array $headers = [])
{
return $this->response($path, $name, $headers, 'attachment');
}







protected function fallbackName($name)
{
return str_replace('%', '', Str::ascii($name));
}









public function put($path, $contents, $options = [])
{
$options = is_string($options)
? ['visibility' => $options]
: (array) $options;




if ($contents instanceof File ||
$contents instanceof UploadedFile) {
return $this->putFile($path, $contents, $options);
}

try {
if ($contents instanceof StreamInterface) {
$this->driver->writeStream($path, $contents->detach(), $options);

return true;
}

is_resource($contents)
? $this->driver->writeStream($path, $contents, $options)
: $this->driver->write($path, $contents, $options);
} catch (UnableToWriteFile|UnableToSetVisibility $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}









public function putFile($path, $file = null, $options = [])
{
if (is_null($file) || is_array($file)) {
[$path, $file, $options] = ['', $path, $file ?? []];
}

$file = is_string($file) ? new File($file) : $file;

return $this->putFileAs($path, $file, $file->hashName(), $options);
}










public function putFileAs($path, $file, $name = null, $options = [])
{
if (is_null($name) || is_array($name)) {
[$path, $file, $name, $options] = ['', $path, $file, $name ?? []];
}

$stream = fopen(is_string($file) ? $file : $file->getRealPath(), 'r');




$result = $this->put(
$path = trim($path.'/'.$name, '/'), $stream, $options
);

if (is_resource($stream)) {
fclose($stream);
}

return $result ? $path : false;
}







public function getVisibility($path)
{
if ($this->driver->visibility($path) == Visibility::PUBLIC) {
return FilesystemContract::VISIBILITY_PUBLIC;
}

return FilesystemContract::VISIBILITY_PRIVATE;
}








public function setVisibility($path, $visibility)
{
try {
$this->driver->setVisibility($path, $this->parseVisibility($visibility));
} catch (UnableToSetVisibility $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}









public function prepend($path, $data, $separator = PHP_EOL)
{
if ($this->fileExists($path)) {
return $this->put($path, $data.$separator.$this->get($path));
}

return $this->put($path, $data);
}









public function append($path, $data, $separator = PHP_EOL)
{
if ($this->fileExists($path)) {
return $this->put($path, $this->get($path).$separator.$data);
}

return $this->put($path, $data);
}







public function delete($paths)
{
$paths = is_array($paths) ? $paths : func_get_args();

$success = true;

foreach ($paths as $path) {
try {
$this->driver->delete($path);
} catch (UnableToDeleteFile $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

$success = false;
}
}

return $success;
}








public function copy($from, $to)
{
try {
$this->driver->copy($from, $to);
} catch (UnableToCopyFile $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}








public function move($from, $to)
{
try {
$this->driver->move($from, $to);
} catch (UnableToMoveFile $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}







public function size($path)
{
return $this->driver->fileSize($path);
}








public function checksum(string $path, array $options = [])
{
try {
return $this->driver->checksum($path, $options);
} catch (UnableToProvideChecksum $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}
}







public function mimeType($path)
{
try {
return $this->driver->mimeType($path);
} catch (UnableToRetrieveMetadata $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);
}

return false;
}







public function lastModified($path)
{
return $this->driver->lastModified($path);
}




public function readStream($path)
{
try {
return $this->driver->readStream($path);
} catch (UnableToReadFile $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);
}
}




public function writeStream($path, $resource, array $options = [])
{
try {
$this->driver->writeStream($path, $resource, $options);
} catch (UnableToWriteFile|UnableToSetVisibility $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}









public function url($path)
{
if (isset($this->config['prefix'])) {
$path = $this->concatPathToUrl($this->config['prefix'], $path);
}

$adapter = $this->adapter;

if (method_exists($adapter, 'getUrl')) {
return $adapter->getUrl($path);
} elseif (method_exists($this->driver, 'getUrl')) {
return $this->driver->getUrl($path);
} elseif ($adapter instanceof FtpAdapter || $adapter instanceof SftpAdapter) {
return $this->getFtpUrl($path);
} elseif ($adapter instanceof LocalAdapter) {
return $this->getLocalUrl($path);
} else {
throw new RuntimeException('This driver does not support retrieving URLs.');
}
}







protected function getFtpUrl($path)
{
return isset($this->config['url'])
? $this->concatPathToUrl($this->config['url'], $path)
: $path;
}







protected function getLocalUrl($path)
{



if (isset($this->config['url'])) {
return $this->concatPathToUrl($this->config['url'], $path);
}

$path = '/storage/'.$path;




if (str_contains($path, '/storage/public/')) {
return Str::replaceFirst('/public/', '/', $path);
}

return $path;
}






public function providesTemporaryUrls()
{
return method_exists($this->adapter, 'getTemporaryUrl') || isset($this->temporaryUrlCallback);
}











public function temporaryUrl($path, $expiration, array $options = [])
{
if (method_exists($this->adapter, 'getTemporaryUrl')) {
return $this->adapter->getTemporaryUrl($path, $expiration, $options);
}

if ($this->temporaryUrlCallback) {
return $this->temporaryUrlCallback->bindTo($this, static::class)(
$path, $expiration, $options
);
}

throw new RuntimeException('This driver does not support creating temporary URLs.');
}











public function temporaryUploadUrl($path, $expiration, array $options = [])
{
if (method_exists($this->adapter, 'temporaryUploadUrl')) {
return $this->adapter->temporaryUploadUrl($path, $expiration, $options);
}

throw new RuntimeException('This driver does not support creating temporary upload URLs.');
}








protected function concatPathToUrl($url, $path)
{
return rtrim($url, '/').'/'.ltrim($path, '/');
}








protected function replaceBaseUrl($uri, $url)
{
$parsed = parse_url($url);

return $uri
->withScheme($parsed['scheme'])
->withHost($parsed['host'])
->withPort($parsed['port'] ?? null);
}








public function files($directory = null, $recursive = false)
{
return $this->driver->listContents($directory ?? '', $recursive)
->filter(function (StorageAttributes $attributes) {
return $attributes->isFile();
})
->sortByPath()
->map(function (StorageAttributes $attributes) {
return $attributes->path();
})
->toArray();
}







public function allFiles($directory = null)
{
return $this->files($directory, true);
}








public function directories($directory = null, $recursive = false)
{
return $this->driver->listContents($directory ?? '', $recursive)
->filter(function (StorageAttributes $attributes) {
return $attributes->isDir();
})
->map(function (StorageAttributes $attributes) {
return $attributes->path();
})
->toArray();
}







public function allDirectories($directory = null)
{
return $this->directories($directory, true);
}







public function makeDirectory($path)
{
try {
$this->driver->createDirectory($path);
} catch (UnableToCreateDirectory|UnableToSetVisibility $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}







public function deleteDirectory($directory)
{
try {
$this->driver->deleteDirectory($directory);
} catch (UnableToDeleteDirectory $e) {
throw_if($this->throwsExceptions(), $e);

$this->report($e);

return false;
}

return true;
}






public function getDriver()
{
return $this->driver;
}






public function getAdapter()
{
return $this->adapter;
}






public function getConfig()
{
return $this->config;
}









protected function parseVisibility($visibility)
{
if (is_null($visibility)) {
return;
}

return match ($visibility) {
FilesystemContract::VISIBILITY_PUBLIC => Visibility::PUBLIC,
FilesystemContract::VISIBILITY_PRIVATE => Visibility::PRIVATE,
default => throw new InvalidArgumentException("Unknown visibility: {$visibility}."),
};
}







public function serveUsing(Closure $callback)
{
$this->serveCallback = $callback;
}







public function buildTemporaryUrlsUsing(Closure $callback)
{
$this->temporaryUrlCallback = $callback;
}






protected function throwsExceptions(): bool
{
return (bool) ($this->config['throw'] ?? false);
}







protected function report($exception)
{
if ($this->shouldReport() && Container::getInstance()->bound(ExceptionHandler::class)) {
Container::getInstance()->make(ExceptionHandler::class)->report($exception);
}
}






protected function shouldReport(): bool
{
return (bool) ($this->config['report'] ?? false);
}










public function __call($method, $parameters)
{
if (static::hasMacro($method)) {
return $this->macroCall($method, $parameters);
}

return $this->driver->{$method}(...$parameters);
}
}
