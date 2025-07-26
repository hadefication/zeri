<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class FileDoesNotExistException extends RuntimeException implements Exception
{
public function __construct(string $file)
{
parent::__construct(
sprintf(
'File "%s" does not exist',
$file,
),
);
}
}
