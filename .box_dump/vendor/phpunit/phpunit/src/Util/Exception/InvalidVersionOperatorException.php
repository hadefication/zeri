<?php declare(strict_types=1);








namespace PHPUnit\Util;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments


*/
final class InvalidVersionOperatorException extends RuntimeException implements Exception
{
public function __construct(string $operator)
{
parent::__construct(
sprintf(
'"%s" is not a valid version_compare() operator',
$operator,
),
);
}
}
