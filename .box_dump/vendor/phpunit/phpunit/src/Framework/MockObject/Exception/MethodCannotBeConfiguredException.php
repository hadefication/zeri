<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class MethodCannotBeConfiguredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(string $method)
{
parent::__construct(
sprintf(
'Trying to configure method "%s" which cannot be configured because it does not exist, has not been specified, is final, or is static',
$method,
),
);
}
}
