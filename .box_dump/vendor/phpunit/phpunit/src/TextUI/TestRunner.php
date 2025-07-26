<?php declare(strict_types=1);








namespace PHPUnit\TextUI;

use function mt_srand;
use PHPUnit\Event;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\ResultCache\ResultCache;
use PHPUnit\Runner\TestSuiteSorter;
use PHPUnit\TextUI\Configuration\Configuration;
use Throwable;

/**
@no-named-arguments


*/
final class TestRunner
{



public function run(Configuration $configuration, ResultCache $resultCache, TestSuite $suite): void
{
try {
Event\Facade::emitter()->testRunnerStarted();

if ($configuration->executionOrder() === TestSuiteSorter::ORDER_RANDOMIZED) {
mt_srand($configuration->randomOrderSeed());
}

if ($configuration->executionOrder() !== TestSuiteSorter::ORDER_DEFAULT ||
$configuration->executionOrderDefects() !== TestSuiteSorter::ORDER_DEFAULT ||
$configuration->resolveDependencies()) {
$resultCache->load();

(new TestSuiteSorter($resultCache))->reorderTestsInSuite(
$suite,
$configuration->executionOrder(),
$configuration->resolveDependencies(),
$configuration->executionOrderDefects(),
);

Event\Facade::emitter()->testSuiteSorted(
$configuration->executionOrder(),
$configuration->executionOrderDefects(),
$configuration->resolveDependencies(),
);
}

(new TestSuiteFilterProcessor)->process($configuration, $suite);

Event\Facade::emitter()->testRunnerExecutionStarted(
Event\TestSuite\TestSuiteBuilder::from($suite),
);

$suite->run();

Event\Facade::emitter()->testRunnerExecutionFinished();
Event\Facade::emitter()->testRunnerFinished();
} catch (Throwable $t) {
throw new RuntimeException(
$t->getMessage(),
(int) $t->getCode(),
$t,
);
}
}
}
