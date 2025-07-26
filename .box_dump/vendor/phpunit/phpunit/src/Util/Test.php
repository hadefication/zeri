<?php declare(strict_types=1);








namespace PHPUnit\Util;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const DEBUG_BACKTRACE_PROVIDE_OBJECT;
use function debug_backtrace;
use function str_starts_with;
use PHPUnit\Event\Code\NoTestCaseObjectOnCallStackException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Parser\Registry;
use ReflectionMethod;

/**
@no-named-arguments


*/
final readonly class Test
{



public static function currentTestCase(): TestCase
{
foreach (debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
if (isset($frame['object']) && $frame['object'] instanceof TestCase) {
return $frame['object'];
}
}

throw new NoTestCaseObjectOnCallStackException;
}

public static function isTestMethod(ReflectionMethod $method): bool
{
if (!$method->isPublic()) {
return false;
}

if (str_starts_with($method->getName(), 'test')) {
return true;
}

$metadata = Registry::parser()->forMethod(
$method->getDeclaringClass()->getName(),
$method->getName(),
);

return $metadata->isTest()->isNotEmpty();
}
}
