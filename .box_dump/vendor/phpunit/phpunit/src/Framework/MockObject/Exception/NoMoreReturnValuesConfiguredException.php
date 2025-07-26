<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;

/**
@no-named-arguments


*/
final class NoMoreReturnValuesConfiguredException extends \PHPUnit\Framework\Exception implements Exception
{
public function __construct(Invocation $invocation, int $numberOfConfiguredReturnValues)
{
parent::__construct(
sprintf(
'Only %d return values have been configured for %s::%s()',
$numberOfConfiguredReturnValues,
$invocation->className(),
$invocation->methodName(),
),
);
}
}
