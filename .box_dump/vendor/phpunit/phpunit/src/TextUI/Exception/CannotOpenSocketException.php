<?php declare(strict_types=1);








namespace PHPUnit\TextUI;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class CannotOpenSocketException extends RuntimeException implements Exception
{
public function __construct(string $hostname, int $port)
{
parent::__construct(
sprintf(
'Cannot open socket %s:%d',
$hostname,
$port,
),
);
}
}
