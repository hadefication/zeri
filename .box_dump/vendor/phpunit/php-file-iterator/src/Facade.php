<?php declare(strict_types=1);








namespace SebastianBergmann\FileIterator;

use function array_unique;
use function assert;
use function sort;
use SplFileInfo;

/**
@no-named-arguments
*/
final class Facade
{








public function getFilesAsArray(array|string $paths, array|string $suffixes = '', array|string $prefixes = '', array $exclude = []): array
{
$iterator = (new Factory)->getFileIterator($paths, $suffixes, $prefixes, $exclude);

$files = [];

foreach ($iterator as $file) {
assert($file instanceof SplFileInfo);

$file = $file->getRealPath();

if ($file) {
$files[] = $file;
}
}

$files = array_unique($files);

sort($files);

return $files;
}
}
