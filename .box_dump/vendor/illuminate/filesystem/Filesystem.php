<?php

namespace Illuminate\Filesystem;

use ErrorException;
use FilesystemIterator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;

class Filesystem
{
use Conditionable, Macroable;







public function exists($path)
{
return file_exists($path);
}







public function missing($path)
{
return ! $this->exists($path);
}










public function get($path, $lock = false)
{
if ($this->isFile($path)) {
return $lock ? $this->sharedGet($path) : file_get_contents($path);
}

throw new FileNotFoundException("File does not exist at path {$path}.");
}











public function json($path, $flags = 0, $lock = false)
{
return json_decode($this->get($path, $lock), true, 512, $flags);
}







public function sharedGet($path)
{
$contents = '';

$handle = fopen($path, 'rb');

if ($handle) {
try {
if (flock($handle, LOCK_SH)) {
clearstatcache(true, $path);

$contents = fread($handle, $this->size($path) ?: 1);

flock($handle, LOCK_UN);
}
} finally {
fclose($handle);
}
}

return $contents;
}










public function getRequire($path, array $data = [])
{
if ($this->isFile($path)) {
$__path = $path;
$__data = $data;

return (static function () use ($__path, $__data) {
extract($__data, EXTR_SKIP);

return require $__path;
})();
}

throw new FileNotFoundException("File does not exist at path {$path}.");
}










public function requireOnce($path, array $data = [])
{
if ($this->isFile($path)) {
$__path = $path;
$__data = $data;

return (static function () use ($__path, $__data) {
extract($__data, EXTR_SKIP);

return require_once $__path;
})();
}

throw new FileNotFoundException("File does not exist at path {$path}.");
}









public function lines($path)
{
if (! $this->isFile($path)) {
throw new FileNotFoundException(
"File does not exist at path {$path}."
);
}

return new LazyCollection(function () use ($path) {
$file = new SplFileObject($path);

$file->setFlags(SplFileObject::DROP_NEW_LINE);

while (! $file->eof()) {
yield $file->fgets();
}
});
}








public function hash($path, $algorithm = 'md5')
{
return hash_file($algorithm, $path);
}









public function put($path, $contents, $lock = false)
{
return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
}









public function replace($path, $content, $mode = null)
{

clearstatcache(true, $path);

$path = realpath($path) ?: $path;

$tempPath = tempnam(dirname($path), basename($path));


if (! is_null($mode)) {
chmod($tempPath, $mode);
} else {
chmod($tempPath, 0777 - umask());
}

file_put_contents($tempPath, $content);

rename($tempPath, $path);
}









public function replaceInFile($search, $replace, $path)
{
file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
}








public function prepend($path, $data)
{
if ($this->exists($path)) {
return $this->put($path, $data.$this->get($path));
}

return $this->put($path, $data);
}









public function append($path, $data, $lock = false)
{
return file_put_contents($path, $data, FILE_APPEND | ($lock ? LOCK_EX : 0));
}








public function chmod($path, $mode = null)
{
if ($mode) {
return chmod($path, $mode);
}

return substr(sprintf('%o', fileperms($path)), -4);
}







public function delete($paths)
{
$paths = is_array($paths) ? $paths : func_get_args();

$success = true;

foreach ($paths as $path) {
try {
if (@unlink($path)) {
clearstatcache(false, $path);
} else {
$success = false;
}
} catch (ErrorException) {
$success = false;
}
}

return $success;
}








public function move($path, $target)
{
return rename($path, $target);
}








public function copy($path, $target)
{
return copy($path, $target);
}








public function link($target, $link)
{
if (! windows_os()) {
if (function_exists('symlink')) {
return symlink($target, $link);
} else {
return exec('ln -s '.escapeshellarg($target).' '.escapeshellarg($link)) !== false;
}
}

$mode = $this->isDirectory($target) ? 'J' : 'H';

exec("mklink /{$mode} ".escapeshellarg($link).' '.escapeshellarg($target));
}










public function relativeLink($target, $link)
{
if (! class_exists(SymfonyFilesystem::class)) {
throw new RuntimeException(
'To enable support for relative links, please install the symfony/filesystem package.'
);
}

$relativeTarget = (new SymfonyFilesystem)->makePathRelative($target, dirname($link));

$this->link($this->isFile($target) ? rtrim($relativeTarget, '/') : $relativeTarget, $link);
}







public function name($path)
{
return pathinfo($path, PATHINFO_FILENAME);
}







public function basename($path)
{
return pathinfo($path, PATHINFO_BASENAME);
}







public function dirname($path)
{
return pathinfo($path, PATHINFO_DIRNAME);
}







public function extension($path)
{
return pathinfo($path, PATHINFO_EXTENSION);
}









public function guessExtension($path)
{
if (! class_exists(MimeTypes::class)) {
throw new RuntimeException(
'To enable support for guessing extensions, please install the symfony/mime package.'
);
}

return (new MimeTypes)->getExtensions($this->mimeType($path))[0] ?? null;
}







public function type($path)
{
return filetype($path);
}







public function mimeType($path)
{
return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
}







public function size($path)
{
return filesize($path);
}







public function lastModified($path)
{
return filemtime($path);
}







public function isDirectory($directory)
{
return is_dir($directory);
}








public function isEmptyDirectory($directory, $ignoreDotFiles = false)
{
return ! Finder::create()->ignoreDotFiles($ignoreDotFiles)->in($directory)->depth(0)->hasResults();
}







public function isReadable($path)
{
return is_readable($path);
}







public function isWritable($path)
{
return is_writable($path);
}








public function hasSameHash($firstFile, $secondFile)
{
$hash = @hash_file('xxh128', $firstFile);

return $hash && hash_equals($hash, (string) @hash_file('xxh128', $secondFile));
}







public function isFile($file)
{
return is_file($file);
}








public function glob($pattern, $flags = 0)
{
return glob($pattern, $flags);
}








public function files($directory, $hidden = false)
{
return iterator_to_array(
Finder::create()->files()->ignoreDotFiles(! $hidden)->in($directory)->depth(0)->sortByName(),
false
);
}








public function allFiles($directory, $hidden = false)
{
return iterator_to_array(
Finder::create()->files()->ignoreDotFiles(! $hidden)->in($directory)->sortByName(),
false
);
}







public function directories($directory)
{
$directories = [];

foreach (Finder::create()->in($directory)->directories()->depth(0)->sortByName() as $dir) {
$directories[] = $dir->getPathname();
}

return $directories;
}









public function ensureDirectoryExists($path, $mode = 0755, $recursive = true)
{
if (! $this->isDirectory($path)) {
$this->makeDirectory($path, $mode, $recursive);
}
}










public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
{
if ($force) {
return @mkdir($path, $mode, $recursive);
}

return mkdir($path, $mode, $recursive);
}









public function moveDirectory($from, $to, $overwrite = false)
{
if ($overwrite && $this->isDirectory($to) && ! $this->deleteDirectory($to)) {
return false;
}

return @rename($from, $to) === true;
}









public function copyDirectory($directory, $destination, $options = null)
{
if (! $this->isDirectory($directory)) {
return false;
}

$options = $options ?: FilesystemIterator::SKIP_DOTS;




$this->ensureDirectoryExists($destination, 0777);

$items = new FilesystemIterator($directory, $options);

foreach ($items as $item) {



$target = $destination.'/'.$item->getBasename();

if ($item->isDir()) {
$path = $item->getPathname();

if (! $this->copyDirectory($path, $target, $options)) {
return false;
}
}




elseif (! $this->copy($item->getPathname(), $target)) {
return false;
}
}

return true;
}










public function deleteDirectory($directory, $preserve = false)
{
if (! $this->isDirectory($directory)) {
return false;
}

$items = new FilesystemIterator($directory);

foreach ($items as $item) {



if ($item->isDir() && ! $item->isLink()) {
$this->deleteDirectory($item->getPathname());
}




else {
$this->delete($item->getPathname());
}
}

unset($items);

if (! $preserve) {
@rmdir($directory);
}

return true;
}







public function deleteDirectories($directory)
{
$allDirectories = $this->directories($directory);

if (! empty($allDirectories)) {
foreach ($allDirectories as $directoryName) {
$this->deleteDirectory($directoryName);
}

return true;
}

return false;
}







public function cleanDirectory($directory)
{
return $this->deleteDirectory($directory, true);
}
}
