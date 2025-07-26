<?php declare(strict_types=1);








namespace SebastianBergmann\FileIterator;

use function assert;
use function preg_match;
use function realpath;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use FilterIterator;
use SplFileInfo;

/**
@template-extends


*/
final class Iterator extends FilterIterator
{
public const PREFIX = 0;
public const SUFFIX = 1;
private false|string $basePath;




private array $suffixes;




private array $prefixes;





public function __construct(string $basePath, \Iterator $iterator, array $suffixes = [], array $prefixes = [])
{
$this->basePath = realpath($basePath);
$this->prefixes = $prefixes;
$this->suffixes = $suffixes;

parent::__construct($iterator);
}

public function accept(): bool
{
$current = $this->getInnerIterator()->current();

assert($current instanceof SplFileInfo);

$filename = $current->getFilename();
$realPath = $current->getRealPath();

if ($realPath === false) {

return false;

}

return $this->acceptPath($realPath) &&
$this->acceptPrefix($filename) &&
$this->acceptSuffix($filename);
}

private function acceptPath(string $path): bool
{

if (preg_match('=/\.[^/]*/=', str_replace((string) $this->basePath, '', $path))) {
return false;
}

return true;
}

private function acceptPrefix(string $filename): bool
{
return $this->acceptSubString($filename, $this->prefixes, self::PREFIX);
}

private function acceptSuffix(string $filename): bool
{
return $this->acceptSubString($filename, $this->suffixes, self::SUFFIX);
}




private function acceptSubString(string $filename, array $subStrings, int $type): bool
{
if (empty($subStrings)) {
return true;
}

foreach ($subStrings as $string) {
if (($type === self::PREFIX && str_starts_with($filename, $string)) ||
($type === self::SUFFIX && str_ends_with($filename, $string))) {
return true;
}
}

return false;
}
}
