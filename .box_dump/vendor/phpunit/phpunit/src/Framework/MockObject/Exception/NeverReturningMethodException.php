<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function sprintf;
use RuntimeException;

/**
@no-named-arguments
*/
final class NeverReturningMethodException extends RuntimeException implements Exception
{




public function __construct(string $className, string $methodName)
{
parent::__construct(
sprintf(
'Method %s::%s() is declared to never return',
$className,
$methodName,
),
);
}
}
