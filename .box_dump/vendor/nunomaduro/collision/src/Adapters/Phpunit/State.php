<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;




final class State
{





public array $suiteTests = [];




public ?string $testCaseName;






public array $testCaseTests = [];






public array $toBePrintedCaseTests = [];




public bool $headerPrinted = false;




public function __construct()
{
$this->testCaseName = '';
}




public function existsInTestCase(Test $test): bool
{
return isset($this->testCaseTests[$test->id()]);
}




public function add(TestResult $test): void
{
$this->testCaseName = $test->testCaseName;

$levels = array_flip([
TestResult::PASS,
TestResult::RUNS,
TestResult::TODO,
TestResult::SKIPPED,
TestResult::WARN,
TestResult::NOTICE,
TestResult::DEPRECATED,
TestResult::RISKY,
TestResult::INCOMPLETE,
TestResult::FAIL,
]);

if (isset($this->testCaseTests[$test->id])) {
$existing = $this->testCaseTests[$test->id];

if ($levels[$existing->type] >= $levels[$test->type]) {
return;
}
}

$this->testCaseTests[$test->id] = $test;
$this->toBePrintedCaseTests[$test->id] = $test;

$this->suiteTests[$test->id] = $test;
}




public function setDuration(Test $test, float $duration): TestResult
{
$result = $this->testCaseTests[$test->id()];

$result->setDuration($duration);

return $result;
}




public function getTestCaseTitle(): string
{
foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::FAIL) {
return 'FAIL';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type !== TestResult::PASS && $test->type !== TestResult::TODO && $test->type !== TestResult::DEPRECATED && $test->type !== TestResult::NOTICE) {
return 'WARN';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::NOTICE) {
return 'NOTI';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::DEPRECATED) {
return 'DEPR';
}
}

if ($this->todosCount() > 0 && (count($this->testCaseTests) === $this->todosCount())) {
return 'TODO';
}

return 'PASS';
}




public function todosCount(): int
{
return count(array_values(array_filter($this->testCaseTests, function (TestResult $test): bool {
return $test->type === TestResult::TODO;
})));
}




public function getTestCaseFontColor(): string
{
if ($this->getTestCaseTitleColor() === 'blue') {
return 'white';
}

return $this->getTestCaseTitle() === 'FAIL' ? 'default' : 'black';
}




public function getTestCaseTitleColor(): string
{
foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::FAIL) {
return 'red';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type !== TestResult::PASS && $test->type !== TestResult::TODO && $test->type !== TestResult::DEPRECATED) {
return 'yellow';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::DEPRECATED) {
return 'yellow';
}
}

foreach ($this->testCaseTests as $test) {
if ($test->type === TestResult::TODO) {
return 'blue';
}
}

return 'green';
}




public function testCaseTestsCount(): int
{
return count($this->testCaseTests);
}




public function testSuiteTestsCount(): int
{
return count($this->suiteTests);
}




public function testCaseHasChanged(TestMethod $test): bool
{
return self::getPrintableTestCaseName($test) !== $this->testCaseName;
}




public function moveTo(TestMethod $test): void
{
$this->testCaseName = self::getPrintableTestCaseName($test);

$this->testCaseTests = [];

$this->headerPrinted = false;
}




public function eachTestCaseTests(callable $callback): void
{
foreach ($this->toBePrintedCaseTests as $test) {
$callback($test);
}

$this->toBePrintedCaseTests = [];
}

public function countTestsInTestSuiteBy(string $type): int
{
return count(array_filter($this->suiteTests, function (TestResult $testResult) use ($type) {
return $testResult->type === $type;
}));
}




public static function getPrintableTestCaseName(TestMethod $test): string
{
$className = explode('::', $test->id())[0];

if (is_subclass_of($className, HasPrintableTestCaseName::class)) {
return $className::getPrintableTestCaseName();
}

return $className;
}
}
