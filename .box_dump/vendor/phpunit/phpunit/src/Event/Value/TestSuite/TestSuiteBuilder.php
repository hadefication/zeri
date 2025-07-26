<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use function assert;
use function class_exists;
use function explode;
use function method_exists;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestCollection;
use PHPUnit\Event\RuntimeException;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite as FrameworkTestSuite;
use PHPUnit\Runner\PhptTestCase;
use ReflectionClass;
use ReflectionMethod;

/**
@no-named-arguments


*/
final readonly class TestSuiteBuilder
{



public static function from(FrameworkTestSuite $testSuite): TestSuite
{
$tests = [];

self::process($testSuite, $tests);

if ($testSuite instanceof DataProviderTestSuite) {
[$className, $methodName] = explode('::', $testSuite->name());

assert(class_exists($className));
assert($methodName !== '' && method_exists($className, $methodName));

$reflector = new ReflectionMethod($className, $methodName);

$file = $reflector->getFileName();
$line = $reflector->getStartLine();

assert($file !== false);
assert($line !== false);

return new TestSuiteForTestMethodWithDataProvider(
$testSuite->name(),
$testSuite->count(),
TestCollection::fromArray($tests),
$className,
$methodName,
$file,
$line,
);
}

if ($testSuite->isForTestClass()) {
$testClassName = $testSuite->name();

assert(class_exists($testClassName));

$reflector = new ReflectionClass($testClassName);

$file = $reflector->getFileName();
$line = $reflector->getStartLine();

assert($file !== false);
assert($line !== false);

return new TestSuiteForTestClass(
$testClassName,
$testSuite->count(),
TestCollection::fromArray($tests),
$file,
$line,
);
}

return new TestSuiteWithName(
$testSuite->name(),
$testSuite->count(),
TestCollection::fromArray($tests),
);
}




private static function process(FrameworkTestSuite $testSuite, array &$tests): void
{
foreach ($testSuite->getIterator() as $test) {
if ($test instanceof FrameworkTestSuite) {
self::process($test, $tests);

continue;
}

if ($test instanceof TestCase || $test instanceof PhptTestCase) {
$tests[] = $test->valueObjectForEvents();
}
}
}
}
