<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Generator;

use function sprintf;

/**
@no-named-arguments


*/
final class CannotUseAddMethodsException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $type, string $methodName)
{
parent::__construct(
sprintf(
'Trying to configure method "%s" with addMethods(), but it exists in class "%s". Use onlyMethods() for methods that exist in the class',
$methodName,
$type,
),
);
}
}
