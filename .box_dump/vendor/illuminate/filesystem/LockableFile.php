<?php

namespace Illuminate\Filesystem;

use Illuminate\Contracts\Filesystem\LockTimeoutException;

class LockableFile
{





protected $handle;






protected $path;






protected $isLocked = false;







public function __construct($path, $mode)
{
$this->path = $path;

$this->ensureDirectoryExists($path);
$this->createResource($path, $mode);
}







protected function ensureDirectoryExists($path)
{
if (! file_exists(dirname($path))) {
@mkdir(dirname($path), 0777, true);
}
}










protected function createResource($path, $mode)
{
$this->handle = fopen($path, $mode);
}







public function read($length = null)
{
clearstatcache(true, $this->path);

return fread($this->handle, $length ?? ($this->size() ?: 1));
}






public function size()
{
return filesize($this->path);
}







public function write($contents)
{
fwrite($this->handle, $contents);

fflush($this->handle);

return $this;
}






public function truncate()
{
rewind($this->handle);

ftruncate($this->handle, 0);

return $this;
}









public function getSharedLock($block = false)
{
if (! flock($this->handle, LOCK_SH | ($block ? 0 : LOCK_NB))) {
throw new LockTimeoutException("Unable to acquire file lock at path [{$this->path}].");
}

$this->isLocked = true;

return $this;
}









public function getExclusiveLock($block = false)
{
if (! flock($this->handle, LOCK_EX | ($block ? 0 : LOCK_NB))) {
throw new LockTimeoutException("Unable to acquire file lock at path [{$this->path}].");
}

$this->isLocked = true;

return $this;
}






public function releaseLock()
{
flock($this->handle, LOCK_UN);

$this->isLocked = false;

return $this;
}






public function close()
{
if ($this->isLocked) {
$this->releaseLock();
}

return fclose($this->handle);
}
}
