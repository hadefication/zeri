<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function array_diff;
use function array_merge;
use function array_reverse;
use function array_splice;
use function count;
use function in_array;
use function max;
use function shuffle;
use function usort;
use PHPUnit\Framework\DataProviderTestSuite;
use PHPUnit\Framework\Reorderable;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\ResultCache\NullResultCache;
use PHPUnit\Runner\ResultCache\ResultCache;

/**
@no-named-arguments


*/
final class TestSuiteSorter
{



public const ORDER_DEFAULT = 0;




public const ORDER_RANDOMIZED = 1;




public const ORDER_REVERSED = 2;




public const ORDER_DEFECTS_FIRST = 3;




public const ORDER_DURATION = 4;




public const ORDER_SIZE = 5;

private const SIZE_SORT_WEIGHT = [
'small' => 1,
'medium' => 2,
'large' => 3,
'unknown' => 4,
];




private array $defectSortOrder = [];
private readonly ResultCache $cache;




private array $originalExecutionOrder = [];




private array $executionOrder = [];

public function __construct(?ResultCache $cache = null)
{
$this->cache = $cache ?? new NullResultCache;
}




public function reorderTestsInSuite(Test $suite, int $order, bool $resolveDependencies, int $orderDefects, bool $isRootTestSuite = true): void
{
$allowedOrders = [
self::ORDER_DEFAULT,
self::ORDER_REVERSED,
self::ORDER_RANDOMIZED,
self::ORDER_DURATION,
self::ORDER_SIZE,
];

if (!in_array($order, $allowedOrders, true)) {
throw new InvalidOrderException;
}

$allowedOrderDefects = [
self::ORDER_DEFAULT,
self::ORDER_DEFECTS_FIRST,
];

if (!in_array($orderDefects, $allowedOrderDefects, true)) {
throw new InvalidOrderException;
}

if ($isRootTestSuite) {
$this->originalExecutionOrder = $this->calculateTestExecutionOrder($suite);
}

if ($suite instanceof TestSuite) {
foreach ($suite as $_suite) {
$this->reorderTestsInSuite($_suite, $order, $resolveDependencies, $orderDefects, false);
}

if ($orderDefects === self::ORDER_DEFECTS_FIRST) {
$this->addSuiteToDefectSortOrder($suite);
}

$this->sort($suite, $order, $resolveDependencies, $orderDefects);
}

if ($isRootTestSuite) {
$this->executionOrder = $this->calculateTestExecutionOrder($suite);
}
}




public function getOriginalExecutionOrder(): array
{
return $this->originalExecutionOrder;
}




public function getExecutionOrder(): array
{
return $this->executionOrder;
}

private function sort(TestSuite $suite, int $order, bool $resolveDependencies, int $orderDefects): void
{
if (empty($suite->tests())) {
return;
}

if ($order === self::ORDER_REVERSED) {
$suite->setTests($this->reverse($suite->tests()));
} elseif ($order === self::ORDER_RANDOMIZED) {
$suite->setTests($this->randomize($suite->tests()));
} elseif ($order === self::ORDER_DURATION) {
$suite->setTests($this->sortByDuration($suite->tests()));
} elseif ($order === self::ORDER_SIZE) {
$suite->setTests($this->sortBySize($suite->tests()));
}

if ($orderDefects === self::ORDER_DEFECTS_FIRST) {
$suite->setTests($this->sortDefectsFirst($suite->tests()));
}

if ($resolveDependencies && !($suite instanceof DataProviderTestSuite)) {
$tests = $suite->tests();

/**
@noinspection */
/**
@phpstan-ignore */
$suite->setTests($this->resolveDependencies($tests));
}
}

private function addSuiteToDefectSortOrder(TestSuite $suite): void
{
$max = 0;

foreach ($suite->tests() as $test) {
if (!$test instanceof Reorderable) {
continue;
}

if (!isset($this->defectSortOrder[$test->sortId()])) {
$this->defectSortOrder[$test->sortId()] = $this->cache->status($test->sortId())->asInt();
$max = max($max, $this->defectSortOrder[$test->sortId()]);
}
}

$this->defectSortOrder[$suite->sortId()] = $max;
}






private function reverse(array $tests): array
{
return array_reverse($tests);
}






private function randomize(array $tests): array
{
shuffle($tests);

return $tests;
}






private function sortDefectsFirst(array $tests): array
{
usort(
$tests,
fn ($left, $right) => $this->cmpDefectPriorityAndTime($left, $right),
);

return $tests;
}






private function sortByDuration(array $tests): array
{
usort(
$tests,
fn ($left, $right) => $this->cmpDuration($left, $right),
);

return $tests;
}






private function sortBySize(array $tests): array
{
usort(
$tests,
fn ($left, $right) => $this->cmpSize($left, $right),
);

return $tests;
}








private function cmpDefectPriorityAndTime(Test $a, Test $b): int
{
if (!($a instanceof Reorderable && $b instanceof Reorderable)) {
return 0;
}

$priorityA = $this->defectSortOrder[$a->sortId()] ?? 0;
$priorityB = $this->defectSortOrder[$b->sortId()] ?? 0;

if ($priorityB <=> $priorityA) {

return $priorityB <=> $priorityA;
}

if ($priorityA || $priorityB) {
return $this->cmpDuration($a, $b);
}


return 0;
}




private function cmpDuration(Test $a, Test $b): int
{
if (!($a instanceof Reorderable && $b instanceof Reorderable)) {
return 0;
}

return $this->cache->time($a->sortId()) <=> $this->cache->time($b->sortId());
}




private function cmpSize(Test $a, Test $b): int
{
$sizeA = ($a instanceof TestCase || $a instanceof DataProviderTestSuite)
? $a->size()->asString()
: 'unknown';
$sizeB = ($b instanceof TestCase || $b instanceof DataProviderTestSuite)
? $b->size()->asString()
: 'unknown';

return self::SIZE_SORT_WEIGHT[$sizeA] <=> self::SIZE_SORT_WEIGHT[$sizeB];
}
















private function resolveDependencies(array $tests): array
{
$newTestOrder = [];
$i = 0;
$provided = [];

do {
if ([] === array_diff($tests[$i]->requires(), $provided)) {
$provided = array_merge($provided, $tests[$i]->provides());
$newTestOrder = array_merge($newTestOrder, array_splice($tests, $i, 1));
$i = 0;
} else {
$i++;
}
} while (!empty($tests) && ($i < count($tests)));

return array_merge($newTestOrder, $tests);
}




private function calculateTestExecutionOrder(Test $suite): array
{
$tests = [];

if ($suite instanceof TestSuite) {
foreach ($suite->tests() as $test) {
if (!$test instanceof TestSuite && $test instanceof Reorderable) {
$tests[] = $test->sortId();
} else {
$tests = array_merge($tests, $this->calculateTestExecutionOrder($test));
}
}
}

return $tests;
}
}
