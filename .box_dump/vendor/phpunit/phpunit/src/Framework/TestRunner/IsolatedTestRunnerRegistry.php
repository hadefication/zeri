<?php declare(strict_types=1);








namespace PHPUnit\Framework;

/**
@no-named-arguments


*/
final class IsolatedTestRunnerRegistry
{
private static ?IsolatedTestRunner $runner = null;

public static function run(TestCase $test, bool $runEntireClass, bool $preserveGlobalState): void
{
if (self::$runner === null) {
self::$runner = new SeparateProcessTestRunner;
}

self::$runner->run($test, $runEntireClass, $preserveGlobalState);
}

public static function set(IsolatedTestRunner $runner): void
{
self::$runner = $runner;
}
}
