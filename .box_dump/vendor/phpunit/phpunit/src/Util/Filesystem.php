<?php declare(strict_types=1);








namespace PHPUnit\Util;

use const DIRECTORY_SEPARATOR;
use function basename;
use function dirname;
use function is_dir;
use function mkdir;
use function realpath;
use function str_starts_with;

/**
@no-named-arguments


*/
final readonly class Filesystem
{
public static function createDirectory(string $directory): bool
{
return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
}






public static function resolveStreamOrFile(string $path): false|string
{
if (str_starts_with($path, 'php://') || str_starts_with($path, 'socket://')) {
return $path;
}

$directory = dirname($path);

if (is_dir($directory)) {
return realpath($directory) . DIRECTORY_SEPARATOR . basename($path);
}

return false;
}
}
