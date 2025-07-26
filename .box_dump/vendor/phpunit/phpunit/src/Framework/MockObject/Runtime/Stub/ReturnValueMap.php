<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Stub;

use function array_pop;
use function count;
use function is_array;
use PHPUnit\Framework\MockObject\Invocation;

/**
@no-named-arguments


*/
final readonly class ReturnValueMap implements Stub
{



private array $valueMap;




public function __construct(array $valueMap)
{
$this->valueMap = $valueMap;
}

public function invoke(Invocation $invocation): mixed
{
$parameterCount = count($invocation->parameters());

foreach ($this->valueMap as $map) {
if (!is_array($map) || $parameterCount !== (count($map) - 1)) {
continue;
}

$return = array_pop($map);

if ($invocation->parameters() === $map) {
return $return;
}
}

return null;
}
}
