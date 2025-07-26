<?php

namespace Illuminate\Foundation\Exceptions\Renderer;

use Illuminate\Foundation\Concerns\ResolvesDumpSource;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class Frame
{
use ResolvesDumpSource;






protected $exception;






protected $classMap;






protected $frame;






protected $basePath;









public function __construct(FlattenException $exception, array $classMap, array $frame, string $basePath)
{
$this->exception = $exception;
$this->classMap = $classMap;
$this->frame = $frame;
$this->basePath = $basePath;
}






public function source()
{
return match (true) {
is_string($this->class()) => $this->class(),
default => $this->file(),
};
}






public function editorHref()
{
return $this->resolveSourceHref($this->frame['file'], $this->line());
}






public function class()
{
$class = array_search((string) realpath($this->frame['file']), $this->classMap, true);

return $class === false ? null : $class;
}






public function file()
{
return str_replace($this->basePath.'/', '', $this->frame['file']);
}






public function line()
{
if (! is_file($this->frame['file']) || ! is_readable($this->frame['file'])) {
return 0;
}

$maxLines = count(file($this->frame['file']) ?: []);

return $this->frame['line'] > $maxLines ? 1 : $this->frame['line'];
}






public function callable()
{
return match (true) {
! empty($this->frame['function']) => $this->frame['function'],
default => 'throw',
};
}






public function snippet()
{
if (! is_file($this->frame['file']) || ! is_readable($this->frame['file'])) {
return '';
}

$contents = file($this->frame['file']) ?: [];

$start = max($this->line() - 6, 0);

$length = 8 * 2 + 1;

return implode('', array_slice($contents, $start, $length));
}






public function isFromVendor()
{
return ! str_starts_with($this->frame['file'], $this->basePath)
|| str_starts_with($this->frame['file'], $this->basePath.'/vendor');
}
}
