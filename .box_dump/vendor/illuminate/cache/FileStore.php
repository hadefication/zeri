<?php

namespace Illuminate\Cache;

use Exception;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Filesystem\LockTimeoutException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\LockableFile;
use Illuminate\Support\InteractsWithTime;

class FileStore implements Store, LockProvider
{
use InteractsWithTime, RetrievesMultipleKeys;






protected $files;






protected $directory;






protected $lockDirectory;






protected $filePermission;








public function __construct(Filesystem $files, $directory, $filePermission = null)
{
$this->files = $files;
$this->directory = $directory;
$this->filePermission = $filePermission;
}







public function get($key)
{
return $this->getPayload($key)['data'] ?? null;
}









public function put($key, $value, $seconds)
{
$this->ensureCacheDirectoryExists($path = $this->path($key));

$result = $this->files->put(
$path, $this->expiration($seconds).serialize($value), true
);

if ($result !== false && $result > 0) {
$this->ensurePermissionsAreCorrect($path);

return true;
}

return false;
}









public function add($key, $value, $seconds)
{
$this->ensureCacheDirectoryExists($path = $this->path($key));

$file = new LockableFile($path, 'c+');

try {
$file->getExclusiveLock();
} catch (LockTimeoutException) {
$file->close();

return false;
}

$expire = $file->read(10);

if (empty($expire) || $this->currentTime() >= $expire) {
$file->truncate()
->write($this->expiration($seconds).serialize($value))
->close();

$this->ensurePermissionsAreCorrect($path);

return true;
}

$file->close();

return false;
}







protected function ensureCacheDirectoryExists($path)
{
$directory = dirname($path);

if (! $this->files->exists($directory)) {
$this->files->makeDirectory($directory, 0777, true, true);


$this->ensurePermissionsAreCorrect($directory);
$this->ensurePermissionsAreCorrect(dirname($directory));
}
}







protected function ensurePermissionsAreCorrect($path)
{
if (is_null($this->filePermission) ||
intval($this->files->chmod($path), 8) == $this->filePermission) {
return;
}

$this->files->chmod($path, $this->filePermission);
}








public function increment($key, $value = 1)
{
$raw = $this->getPayload($key);

return tap(((int) $raw['data']) + $value, function ($newValue) use ($key, $raw) {
$this->put($key, $newValue, $raw['time'] ?? 0);
});
}








public function decrement($key, $value = 1)
{
return $this->increment($key, $value * -1);
}








public function forever($key, $value)
{
return $this->put($key, $value, 0);
}









public function lock($name, $seconds = 0, $owner = null)
{
$this->ensureCacheDirectoryExists($this->lockDirectory ?? $this->directory);

return new FileLock(
new static($this->files, $this->lockDirectory ?? $this->directory, $this->filePermission),
$name,
$seconds,
$owner
);
}








public function restoreLock($name, $owner)
{
return $this->lock($name, 0, $owner);
}







public function forget($key)
{
if ($this->files->exists($file = $this->path($key))) {
return tap($this->files->delete($file), function ($forgotten) use ($key) {
if ($forgotten && $this->files->exists($file = $this->path("illuminate:cache:flexible:created:{$key}"))) {
$this->files->delete($file);
}
});
}

return false;
}






public function flush()
{
if (! $this->files->isDirectory($this->directory)) {
return false;
}

foreach ($this->files->directories($this->directory) as $directory) {
$deleted = $this->files->deleteDirectory($directory);

if (! $deleted || $this->files->exists($directory)) {
return false;
}
}

return true;
}







protected function getPayload($key)
{
$path = $this->path($key);




try {
if (is_null($contents = $this->files->get($path, true))) {
return $this->emptyPayload();
}

$expire = substr($contents, 0, 10);
} catch (Exception) {
return $this->emptyPayload();
}




if ($this->currentTime() >= $expire) {
$this->forget($key);

return $this->emptyPayload();
}

try {
$data = unserialize(substr($contents, 10));
} catch (Exception) {
$this->forget($key);

return $this->emptyPayload();
}




$time = $expire - $this->currentTime();

return compact('data', 'time');
}






protected function emptyPayload()
{
return ['data' => null, 'time' => null];
}







public function path($key)
{
$parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

return $this->directory.'/'.implode('/', $parts).'/'.$hash;
}







protected function expiration($seconds)
{
$time = $this->availableAt($seconds);

return $seconds === 0 || $time > 9999999999 ? 9999999999 : $time;
}






public function getFilesystem()
{
return $this->files;
}






public function getDirectory()
{
return $this->directory;
}







public function setDirectory($directory)
{
$this->directory = $directory;

return $this;
}







public function setLockDirectory($lockDirectory)
{
$this->lockDirectory = $lockDirectory;

return $this;
}






public function getPrefix()
{
return '';
}
}
