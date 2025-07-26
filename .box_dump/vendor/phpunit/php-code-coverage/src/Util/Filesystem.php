<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Util;

use function is_dir;
use function mkdir;
use function sprintf;




final class Filesystem
{



public static function createDirectory(string $directory): void
{
$success = !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));

if (!$success) {
throw new DirectoryCouldNotBeCreatedException(
sprintf(
'Directory "%s" could not be created',
$directory,
),
);
}
}
}
