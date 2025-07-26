<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class ParameterDoesNotExistException extends RuntimeException implements Exception
{
public function __construct(string $name)
{
parent::__construct(
sprintf(
'Parameter "%s" does not exist',
$name,
),
);
}
}
