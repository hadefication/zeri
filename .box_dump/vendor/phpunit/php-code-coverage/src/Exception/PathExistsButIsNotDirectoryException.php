<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Driver;

use function sprintf;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class PathExistsButIsNotDirectoryException extends RuntimeException implements Exception
{
public function __construct(string $path)
{
parent::__construct(sprintf('"%s" exists but is not a directory', $path));
}
}
