<?php

declare(strict_types=1);

namespace Dotenv\Store\File;




final class Paths
{







private function __construct()
{

}









public static function filePaths(array $paths, array $names)
{
$files = [];

foreach ($paths as $path) {
foreach ($names as $name) {
$files[] = \rtrim($path, \DIRECTORY_SEPARATOR).\DIRECTORY_SEPARATOR.$name;
}
}

return $files;
}
}
