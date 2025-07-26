<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use const PHP_EOL;
use function array_merge;
use function array_pop;
use function array_reverse;
use function assert;
use function call_user_func;
use function class_exists;
use function count;
use function implode;
use function is_callable;
use function is_file;
use function is_subclass_of;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function trim;
use Iterator;
use IteratorAggregate;
use PHPUnit\Event;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Metadata\Api\Dependencies;
use PHPUnit\Metadata\Api\Groups;
use PHPUnit\Metadata\Api\HookMethods;
use PHPUnit\Metadata\Api\Requirements;
use PHPUnit\Metadata\MetadataCollection;
use PHPUnit\Runner\Exception as RunnerException;
use PHPUnit\Runner\Filter\Factory;
use PHPUnit\Runner\PhptTestCase;
use PHPUnit\Runner\TestSuiteLoader;
use PHPUnit\TestRunner\TestResult\Facade as TestResultFacade;
use PHPUnit\Util\Filter;
use PHPUnit\Util\Reflection;
use PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionMethod;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;
use SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException;
use Throwable;

/**
@template-implements
@no-named-arguments



*/
class TestSuite implements IteratorAggregate, Reorderable, Test
{



private string $name;




private array $groups = [];




private ?array $requiredTests = null;




private array $tests = [];




private ?array $providedTests = null;
private ?Factory $iteratorFilter = null;
private bool $wasRun = false;




public static function empty(string $name): static
{
return new static($name);
}





public static function fromClassReflector(ReflectionClass $class, array $groups = []): static
{
$testSuite = new static($class->getName());

foreach (Reflection::publicMethodsDeclaredDirectlyInTestClass($class) as $method) {
if (!TestUtil::isTestMethod($method)) {
continue;
}

$testSuite->addTestMethod($class, $method, $groups);
}

if ($testSuite->isEmpty()) {
Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
sprintf(
'No tests found in class "%s".',
$class->getName(),
),
);
}

return $testSuite;
}




final private function __construct(string $name)
{
$this->name = $name;
}






public function addTest(Test $test, array $groups = []): void
{
if ($test instanceof self) {
$this->tests[] = $test;

$this->clearCaches();

return;
}

assert($test instanceof TestCase || $test instanceof PhptTestCase);

$class = new ReflectionClass($test);

if ($class->isAbstract()) {
return;
}

$this->tests[] = $test;

$this->clearCaches();

if ($this->containsOnlyVirtualGroups($groups)) {
$groups[] = 'default';
}

if ($test instanceof TestCase) {
$id = $test->valueObjectForEvents()->id();

$test->setGroups($groups);
} else {
$id = $test->valueObjectForEvents()->id();
}

foreach ($groups as $group) {
if (!isset($this->groups[$group])) {
$this->groups[$group] = [$id];
} else {
$this->groups[$group][] = $id;
}
}
}









public function addTestSuite(ReflectionClass $testClass, array $groups = []): void
{
if ($testClass->isAbstract()) {
throw new Exception(
sprintf(
'Class %s is abstract',
$testClass->getName(),
),
);
}

if (!$testClass->isSubclassOf(TestCase::class)) {
throw new Exception(
sprintf(
'Class %s is not a subclass of %s',
$testClass->getName(),
TestCase::class,
),
);
}

$this->addTest(self::fromClassReflector($testClass, $groups), $groups);
}













public function addTestFile(string $filename, array $groups = []): void
{
try {
if (str_ends_with($filename, '.phpt') && is_file($filename)) {
$this->addTest(new PhptTestCase($filename));
} else {
$this->addTestSuite(
(new TestSuiteLoader)->load($filename),
$groups,
);
}
} catch (RunnerException $e) {
Event\Facade::emitter()->testRunnerTriggeredPhpunitWarning(
$e->getMessage(),
);
}
}








public function addTestFiles(iterable $fileNames): void
{
foreach ($fileNames as $filename) {
$this->addTestFile((string) $filename);
}
}




public function count(): int
{
$numTests = 0;

foreach ($this as $test) {
$numTests += count($test);
}

return $numTests;
}

public function isEmpty(): bool
{
foreach ($this as $test) {
if (count($test) !== 0) {
return false;
}
}

return true;
}




public function name(): string
{
return $this->name;
}




public function groups(): array
{
return $this->groups;
}




public function collect(): array
{
$tests = [];

foreach ($this as $test) {
if ($test instanceof self) {
$tests = array_merge($tests, $test->collect());

continue;
}

assert($test instanceof TestCase || $test instanceof PhptTestCase);

$tests[] = $test;
}

return $tests;
}









public function run(): void
{
if ($this->wasRun) {

throw new Exception('The tests aggregated by this TestSuite were already run');

}

$this->wasRun = true;

if ($this->isEmpty()) {
return;
}

$emitter = Event\Facade::emitter();
$testSuiteValueObjectForEvents = Event\TestSuite\TestSuiteBuilder::from($this);

$emitter->testSuiteStarted($testSuiteValueObjectForEvents);

if (!$this->invokeMethodsBeforeFirstTest($emitter, $testSuiteValueObjectForEvents)) {
return;
}


$tests = [];

foreach ($this as $test) {
$tests[] = $test;
}

$tests = array_reverse($tests);

$this->tests = [];
$this->groups = [];

while (($test = array_pop($tests)) !== null) {
if (TestResultFacade::shouldStop()) {
$emitter->testRunnerExecutionAborted();

break;
}

$test->run();
}

$this->invokeMethodsAfterLastTest($emitter);

$emitter->testSuiteFinished($testSuiteValueObjectForEvents);
}






public function tests(): array
{
return $this->tests;
}






public function setTests(array $tests): void
{
$this->tests = $tests;
}






public function markTestSuiteSkipped(string $message = ''): never
{
throw new SkippedTestSuiteError($message);
}




public function getIterator(): Iterator
{
$iterator = new TestSuiteIterator($this);

if ($this->iteratorFilter !== null) {
$iterator = $this->iteratorFilter->factory($iterator, $this);
}

return $iterator;
}

public function injectFilter(Factory $filter): void
{
$this->iteratorFilter = $filter;

foreach ($this as $test) {
if ($test instanceof self) {
$test->injectFilter($filter);
}
}
}




public function provides(): array
{
if ($this->providedTests === null) {
$this->providedTests = [];

if (is_callable($this->sortId(), true)) {
$this->providedTests[] = new ExecutionOrderDependency($this->sortId());
}

foreach ($this->tests as $test) {
if (!($test instanceof Reorderable)) {
continue;
}

$this->providedTests = ExecutionOrderDependency::mergeUnique($this->providedTests, $test->provides());
}
}

return $this->providedTests;
}




public function requires(): array
{
if ($this->requiredTests === null) {
$this->requiredTests = [];

foreach ($this->tests as $test) {
if (!($test instanceof Reorderable)) {
continue;
}

$this->requiredTests = ExecutionOrderDependency::mergeUnique(
ExecutionOrderDependency::filterInvalid($this->requiredTests),
$test->requires(),
);
}

$this->requiredTests = ExecutionOrderDependency::diff($this->requiredTests, $this->provides());
}

return $this->requiredTests;
}

public function sortId(): string
{
return $this->name() . '::class';
}

/**
@phpstan-assert-if-true
*/
public function isForTestClass(): bool
{
return class_exists($this->name, false) && is_subclass_of($this->name, TestCase::class);
}







protected function addTestMethod(ReflectionClass $class, ReflectionMethod $method, array $groups): void
{
$className = $class->getName();
$methodName = $method->getName();

assert(!empty($methodName));

try {
$test = (new TestBuilder)->build($class, $methodName, $groups);
} catch (InvalidDataProviderException $e) {
Event\Facade::emitter()->testTriggeredPhpunitError(
new TestMethod(
$className,
$methodName,
$class->getFileName(),
$method->getStartLine(),
Event\Code\TestDoxBuilder::fromClassNameAndMethodName(
$className,
$methodName,
),
MetadataCollection::fromArray([]),
Event\TestData\TestDataCollection::fromArray([]),
),
sprintf(
"The data provider specified for %s::%s is invalid\n%s",
$className,
$methodName,
$this->throwableToString($e),
),
);

return;
}

if ($test instanceof TestCase || $test instanceof DataProviderTestSuite) {
$test->setDependencies(
Dependencies::dependencies($class->getName(), $methodName),
);
}

$this->addTest(
$test,
array_merge(
$groups,
(new Groups)->groups($class->getName(), $methodName),
),
);
}

private function clearCaches(): void
{
$this->providedTests = null;
$this->requiredTests = null;
}




private function containsOnlyVirtualGroups(array $groups): bool
{
foreach ($groups as $group) {
if (!str_starts_with($group, '__phpunit_')) {
return false;
}
}

return true;
}

private function methodDoesNotExistOrIsDeclaredInTestCase(string $methodName): bool
{
$reflector = new ReflectionClass($this->name);

return !$reflector->hasMethod($methodName) ||
$reflector->getMethod($methodName)->getDeclaringClass()->getName() === TestCase::class;
}




private function throwableToString(Throwable $t): string
{
$message = $t->getMessage();

if (empty(trim($message))) {
$message = '<no message>';
}

if ($t instanceof InvalidDataProviderException) {
return sprintf(
"%s\n%s",
$message,
Filter::stackTraceFromThrowableAsString($t),
);
}

return sprintf(
"%s: %s\n%s",
$t::class,
$message,
Filter::stackTraceFromThrowableAsString($t),
);
}





private function invokeMethodsBeforeFirstTest(Event\Emitter $emitter, Event\TestSuite\TestSuite $testSuiteValueObjectForEvents): bool
{
if (!$this->isForTestClass()) {
return true;
}

$methods = (new HookMethods)->hookMethods($this->name)['beforeClass']->methodNamesSortedByPriority();
$calledMethods = [];
$emitCalledEvent = true;
$result = true;

foreach ($methods as $method) {
if ($this->methodDoesNotExistOrIsDeclaredInTestCase($method)) {
continue;
}

$calledMethod = new Event\Code\ClassMethod(
$this->name,
$method,
);

try {
$missingRequirements = (new Requirements)->requirementsNotSatisfiedFor($this->name, $method);

if ($missingRequirements !== []) {
$emitCalledEvent = false;

$this->markTestSuiteSkipped(implode(PHP_EOL, $missingRequirements));
}

call_user_func([$this->name, $method]);
} catch (Throwable $t) {
}

if ($emitCalledEvent) {
$emitter->beforeFirstTestMethodCalled(
$this->name,
$calledMethod,
);

$calledMethods[] = $calledMethod;
}

if (isset($t) && $t instanceof SkippedTest) {
$emitter->testSuiteSkipped(
$testSuiteValueObjectForEvents,
$t->getMessage(),
);

return false;
}

if (isset($t)) {
$emitter->beforeFirstTestMethodErrored(
$this->name,
$calledMethod,
Event\Code\ThrowableBuilder::from($t),
);

$result = false;
}
}

if (!empty($calledMethods)) {
$emitter->beforeFirstTestMethodFinished(
$this->name,
...$calledMethods,
);
}

if (!$result) {
$emitter->testSuiteFinished($testSuiteValueObjectForEvents);
}

return $result;
}

private function invokeMethodsAfterLastTest(Event\Emitter $emitter): void
{
if (!$this->isForTestClass()) {
return;
}

$methods = (new HookMethods)->hookMethods($this->name)['afterClass']->methodNamesSortedByPriority();
$calledMethods = [];

foreach ($methods as $method) {
if ($this->methodDoesNotExistOrIsDeclaredInTestCase($method)) {
continue;
}

$calledMethod = new Event\Code\ClassMethod(
$this->name,
$method,
);

try {
call_user_func([$this->name, $method]);
} catch (Throwable $t) {
}

$emitter->afterLastTestMethodCalled(
$this->name,
$calledMethod,
);

$calledMethods[] = $calledMethod;

if (isset($t)) {
$emitter->afterLastTestMethodErrored(
$this->name,
$calledMethod,
Event\Code\ThrowableBuilder::from($t),
);
}
}

if (!empty($calledMethods)) {
$emitter->afterLastTestMethodFinished(
$this->name,
...$calledMethods,
);
}
}
}
