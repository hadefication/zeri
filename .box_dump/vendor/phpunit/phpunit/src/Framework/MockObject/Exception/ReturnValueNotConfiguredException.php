<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class ReturnValueNotConfiguredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(Invocation $invocation)
{
parent::__construct(
sprintf(
'No return value is configured for %s::%s() and return value generation is disabled',
$invocation->className(),
$invocation->methodName(),
),
);
}
}
