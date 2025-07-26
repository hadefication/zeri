<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use function array_shift;
use function count;
use PHPUnit\Framework\MockObject\Invocation;
use PHPUnit\Framework\MockObject\NoMoreReturnValuesConfiguredException;

/**
@no-named-arguments


*/
final class ConsecutiveCalls implements Stub
{



private array $stack;
private int $numberOfConfiguredReturnValues;




public function __construct(array $stack)
{
$this->stack = $stack;
$this->numberOfConfiguredReturnValues = count($stack);
}




public function invoke(Invocation $invocation): mixed
{
if (empty($this->stack)) {
throw new NoMoreReturnValuesConfiguredException(
$invocation,
$this->numberOfConfiguredReturnValues,
);
}

$value = array_shift($this->stack);

if ($value instanceof Stub) {
$value = $value->invoke($invocation);
}

return $value;
}
}
