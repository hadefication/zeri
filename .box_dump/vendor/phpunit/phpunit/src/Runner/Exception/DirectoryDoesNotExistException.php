<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class DirectoryDoesNotExistException extends RuntimeException implements Exception
{
public function __construct(string $directory)
{
parent::__construct(
sprintf(
'Directory "%s" does not exist and could not be created',
$directory,
),
);
}
}
