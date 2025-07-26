<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class InvalidMethodNameException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $method)
{
parent::__construct(
sprintf(
'Cannot double method with invalid name "%s"',
$method,
),
);
}
}
