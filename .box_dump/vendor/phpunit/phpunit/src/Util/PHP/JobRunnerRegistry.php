<?php declare(strict_types=1);








namespace PHPUnit\Util\PHP;

use PHPUnit\Event\Facade;
use PHPUnit\Framework\ChildProcessResultProcessor;
use PHPUnit\Framework\Test;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\TestRunner\TestResult\PassedTests;

/**
@no-named-arguments


*/
final class JobRunnerRegistry
{
private static ?JobRunner $runner = null;

public static function run(Job $job): Result
{
return self::runner()->run($job);
}




public static function runTestJob(Job $job, string $processResultFile, Test $test): void
{
self::runner()->runTestJob($job, $processResultFile, $test);
}

public static function set(JobRunner $runner): void
{
self::$runner = $runner;
}

private static function runner(): JobRunner
{
if (self::$runner === null) {
self::$runner = new DefaultJobRunner(
new ChildProcessResultProcessor(
Facade::instance(),
Facade::emitter(),
PassedTests::instance(),
CodeCoverage::instance(),
),
);
}

return self::$runner;
}
}
