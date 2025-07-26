<?php declare(strict_types=1);








namespace PHPUnit\Util;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class InvalidDirectoryException extends RuntimeException implements Exception
{
public function __construct(string $directory)
{
parent::__construct(
sprintf(
'"%s" is not a directory',
$directory,
),
);
}
}
