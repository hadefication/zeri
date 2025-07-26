<?php declare(strict_types=1);








namespace PHPUnit\TextUI;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class InvalidSocketException extends RuntimeException implements Exception
{
public function __construct(string $socket)
{
parent::__construct(
sprintf(
'"%s" does not match "socket://hostname:port" format',
$socket,
),
);
}
}
