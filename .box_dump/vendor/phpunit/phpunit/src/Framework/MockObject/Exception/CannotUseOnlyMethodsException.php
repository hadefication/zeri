<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class CannotUseOnlyMethodsException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $type, string $methodName)
{
parent::__construct(
sprintf(
'Trying to configure method "%s" with onlyMethods(), but it does not exist in class "%s"',
$methodName,
$type,
),
);
}
}
