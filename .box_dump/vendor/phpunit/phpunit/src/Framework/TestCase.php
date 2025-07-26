<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use const LC_ALL;
use const LC_COLLATE;
use const LC_CTYPE;
use const LC_MONETARY;
use const LC_NUMERIC;
use const LC_TIME;
use const PATHINFO_FILENAME;
use const PHP_EOL;
use const PHP_URL_PATH;
use function array_keys;
use function array_merge;
use function array_reverse;
use function array_values;
use function assert;
use function basename;
use function chdir;
use function class_exists;
use function clearstatcache;
use function count;
use function defined;
use function error_clear_last;
use function explode;
use function getcwd;
use function implode;
use function in_array;
use function ini_set;
use function is_array;
use function is_callable;
use function is_int;
use function is_object;
use function is_string;
use function libxml_clear_errors;
use function method_exists;
use function ob_end_clean;
use function ob_get_clean;
use function ob_get_contents;
use function ob_get_level;
use function ob_start;
use function parse_url;
use function pathinfo;
use function preg_match;
use function preg_replace;
use function restore_error_handler;
use function restore_exception_handler;
use function set_error_handler;
use function set_exception_handler;
use function setlocale;
use function sprintf;
use function str_contains;
use function trim;
use AssertionError;
use DeepCopy\DeepCopy;
use PHPUnit\Event;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\Constraint\Exception as ExceptionConstraint;
use PHPUnit\Framework\Constraint\ExceptionCode;
use PHPUnit\Framework\Constraint\ExceptionMessageIsOrContains;
use PHPUnit\Framework\Constraint\ExceptionMessageMatchesRegularExpression;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\Generator\Generator as MockGenerator;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\MockObjectInternal;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount as InvokedAtMostCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
use PHPUnit\Framework\TestSize\TestSize;
use PHPUnit\Framework\TestStatus\TestStatus;
use PHPUnit\Metadata\Api\Groups;
use PHPUnit\Metadata\Api\HookMethods;
use PHPUnit\Metadata\Api\Requirements;
use PHPUnit\Metadata\Parser\Registry as MetadataRegistry;
use PHPUnit\Runner\DeprecationCollector\Facade as DeprecationCollector;
use PHPUnit\Runner\HookMethodCollection;
use PHPUnit\TestRunner\TestResult\PassedTests;
use PHPUnit\TextUI\Configuration\Registry as ConfigurationRegistry;
use PHPUnit\Util\Exporter;
use PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use SebastianBergmann\GlobalState\ExcludeList as GlobalStateExcludeList;
use SebastianBergmann\GlobalState\Restorer;
use SebastianBergmann\GlobalState\Snapshot;
use SebastianBergmann\Invoker\TimeoutException;
use SebastianBergmann\ObjectEnumerator\Enumerator;
use Throwable;

/**
@no-named-arguments
*/
abstract class TestCase extends Assert implements Reorderable, SelfDescribing, Test
{
private const LOCALE_CATEGORIES = [LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, LC_TIME];
private ?bool $backupGlobals = null;




private array $backupGlobalsExcludeList = [];
private ?bool $backupStaticProperties = null;




private array $backupStaticPropertiesExcludeList = [];
private ?Snapshot $snapshot = null;




private ?array $backupGlobalErrorHandlers = null;




private ?array $backupGlobalExceptionHandlers = null;
private ?bool $runClassInSeparateProcess = null;
private ?bool $runTestInSeparateProcess = null;
private bool $preserveGlobalState = false;
private bool $inIsolation = false;
private ?string $expectedException = null;
private ?string $expectedExceptionMessage = null;
private ?string $expectedExceptionMessageRegExp = null;
private null|int|string $expectedExceptionCode = null;




private array $providedTests = [];




private array $data = [];
private int|string $dataName = '';




private string $methodName;




private array $groups = [];




private array $dependencies = [];




private array $dependencyInput = [];




private array $iniSettings = [];




private array $locale = [];




private array $mockObjects = [];
private TestStatus $status;
private int $numberOfAssertionsPerformed = 0;
private mixed $testResult = null;
private string $output = '';
private ?string $outputExpectedRegex = null;
private ?string $outputExpectedString = null;
private bool $outputBufferingActive = false;
private int $outputBufferingLevel;
private bool $outputRetrievedForAssertion = false;
private bool $doesNotPerformAssertions = false;




private array $customComparators = [];
private ?Event\Code\TestMethod $testValueObjectForEvents = null;
private bool $wasPrepared = false;




private array $failureTypes = [];




private array $expectedUserDeprecationMessage = [];




private array $expectedUserDeprecationMessageRegularExpression = [];








public function __construct(string $name)
{
$this->methodName = $name;
$this->status = TestStatus::unknown();

if (is_callable($this->sortId(), true)) {
$this->providedTests = [new ExecutionOrderDependency($this->sortId())];
}
}






public static function setUpBeforeClass(): void
{
}






public static function tearDownAfterClass(): void
{
}






protected function setUp(): void
{
}








protected function assertPreConditions(): void
{
}








protected function assertPostConditions(): void
{
}






protected function tearDown(): void
{
}








public function toString(): string
{
$buffer = sprintf(
'%s::%s',
(new ReflectionClass($this))->getName(),
$this->methodName,
);

return $buffer . $this->dataSetAsStringWithData();
}




final public function count(): int
{
return 1;
}




final public function status(): TestStatus
{
return $this->status;
}














final public function run(): void
{
if (!$this->handleDependencies()) {
return;
}

if (!$this->shouldRunInSeparateProcess() || $this->requirementsNotSatisfied()) {
(new TestRunner)->run($this);

return;
}

IsolatedTestRunnerRegistry::run(
$this,
$this->runClassInSeparateProcess && !$this->runTestInSeparateProcess,
$this->preserveGlobalState,
);
}






final public function groups(): array
{
return $this->groups;
}






final public function setGroups(array $groups): void
{
$this->groups = $groups;
}




final public function nameWithDataSet(): string
{
return $this->methodName . $this->dataSetAsString();
}






final public function name(): string
{
return $this->methodName;
}




final public function size(): TestSize
{
return (new Groups)->size(
static::class,
$this->methodName,
);
}




final public function hasUnexpectedOutput(): bool
{
if ($this->output === '') {
return false;
}

if ($this->expectsOutput()) {
return false;
}

return true;
}




final public function output(): string
{
if (!$this->outputBufferingActive) {
return $this->output;
}

return (string) ob_get_contents();
}




final public function doesNotPerformAssertions(): bool
{
return $this->doesNotPerformAssertions;
}




final public function expectsOutput(): bool
{
return $this->hasExpectationOnOutput() || $this->outputRetrievedForAssertion;
}






final public function runBare(): void
{
$emitter = Event\Facade::emitter();

error_clear_last();
clearstatcache();

$emitter->testPreparationStarted(
$this->valueObjectForEvents(),
);

$this->snapshotGlobalState();
$this->snapshotGlobalErrorExceptionHandlers();
$this->startOutputBuffering();

$hookMethods = (new HookMethods)->hookMethods(static::class);
$hasMetRequirements = false;
$this->numberOfAssertionsPerformed = 0;
$currentWorkingDirectory = getcwd();

try {
$this->checkRequirements();
$hasMetRequirements = true;

if ($this->inIsolation) {

$this->invokeBeforeClassHookMethods($hookMethods, $emitter);

}

if (method_exists(static::class, $this->methodName) &&
MetadataRegistry::parser()->forClassAndMethod(static::class, $this->methodName)->isDoesNotPerformAssertions()->isNotEmpty()) {
$this->doesNotPerformAssertions = true;
}

$this->invokeBeforeTestHookMethods($hookMethods, $emitter);
$this->invokePreConditionHookMethods($hookMethods, $emitter);

$emitter->testPrepared(
$this->valueObjectForEvents(),
);

$this->wasPrepared = true;
$this->testResult = $this->runTest();

$this->verifyDeprecationExpectations();
$this->verifyMockObjects();
$this->invokePostConditionHookMethods($hookMethods, $emitter);

$this->status = TestStatus::success();
} catch (IncompleteTest $e) {
$this->status = TestStatus::incomplete($e->getMessage());

$emitter->testMarkedAsIncomplete(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
);
} catch (SkippedTest $e) {
$this->status = TestStatus::skipped($e->getMessage());

$emitter->testSkipped(
$this->valueObjectForEvents(),
$e->getMessage(),
);
} catch (AssertionError|AssertionFailedError $e) {
$this->handleExceptionFromInvokedCountMockObjectRule($e);

if (!$this->wasPrepared) {
$this->wasPrepared = true;

$emitter->testPreparationFailed(
$this->valueObjectForEvents(),
);
}

$this->status = TestStatus::failure($e->getMessage());

$emitter->testFailed(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
Event\Code\ComparisonFailureBuilder::from($e),
);
} catch (TimeoutException $e) {
} catch (Throwable $_e) {
if ($this->isRegisteredFailure($_e)) {
$this->status = TestStatus::failure($_e->getMessage());

$emitter->testFailed(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($_e),
null,
);
} else {
$e = $this->transformException($_e);

$this->status = TestStatus::error($e->getMessage());

if (!$this->wasPrepared) {
$emitter->testPreparationFailed(
$this->valueObjectForEvents(),
);
}

$emitter->testErrored(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
);
}
}

$outputBufferingStopped = false;

if (!isset($e) &&
$this->hasExpectationOnOutput() &&
$this->stopOutputBuffering()) {
$outputBufferingStopped = true;

$this->performAssertionsOnOutput();
}

try {
$this->mockObjects = [];

/**
@phpstan-ignore */
} catch (Throwable $e) {
Event\Facade::emitter()->testErrored(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
);
}



try {
if ($hasMetRequirements) {
$this->invokeAfterTestHookMethods($hookMethods, $emitter);

if ($this->inIsolation) {

$this->invokeAfterClassHookMethods($hookMethods, $emitter);

}
}
} catch (AssertionError|AssertionFailedError $e) {
$this->status = TestStatus::failure($e->getMessage());

$emitter->testFailed(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
Event\Code\ComparisonFailureBuilder::from($e),
);
} catch (Throwable $exceptionRaisedDuringTearDown) {
if (!isset($e)) {
$this->status = TestStatus::error($exceptionRaisedDuringTearDown->getMessage());
$e = $exceptionRaisedDuringTearDown;

$emitter->testErrored(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($exceptionRaisedDuringTearDown),
);
}
}

if (!isset($e) && !isset($_e)) {
$emitter->testPassed(
$this->valueObjectForEvents(),
);

if (!$this->usesDataProvider()) {
PassedTests::instance()->testMethodPassed(
$this->valueObjectForEvents(),
$this->testResult,
);
}
}

if (!$outputBufferingStopped) {
$this->stopOutputBuffering();
}

clearstatcache();

if ($currentWorkingDirectory !== getcwd()) {
chdir($currentWorkingDirectory);
}

$this->restoreGlobalErrorExceptionHandlers();
$this->restoreGlobalState();
$this->unregisterCustomComparators();
$this->cleanupIniSettings();
$this->cleanupLocaleSettings();
libxml_clear_errors();

$this->testValueObjectForEvents = null;

if (isset($e)) {
$this->onNotSuccessfulTest($e);
}
}






final public function setDependencies(array $dependencies): void
{
$this->dependencies = $dependencies;
}








final public function setDependencyInput(array $dependencyInput): void
{
$this->dependencyInput = $dependencyInput;
}






final public function dependencyInput(): array
{
return $this->dependencyInput;
}




final public function hasDependencyInput(): bool
{
return !empty($this->dependencyInput);
}




final public function setBackupGlobals(bool $backupGlobals): void
{
$this->backupGlobals = $backupGlobals;
}






final public function setBackupGlobalsExcludeList(array $backupGlobalsExcludeList): void
{
$this->backupGlobalsExcludeList = $backupGlobalsExcludeList;
}




final public function setBackupStaticProperties(bool $backupStaticProperties): void
{
$this->backupStaticProperties = $backupStaticProperties;
}






final public function setBackupStaticPropertiesExcludeList(array $backupStaticPropertiesExcludeList): void
{
$this->backupStaticPropertiesExcludeList = $backupStaticPropertiesExcludeList;
}




final public function setRunTestInSeparateProcess(bool $runTestInSeparateProcess): void
{
if ($this->runTestInSeparateProcess === null) {
$this->runTestInSeparateProcess = $runTestInSeparateProcess;
}
}




final public function setRunClassInSeparateProcess(bool $runClassInSeparateProcess): void
{
$this->runClassInSeparateProcess = $runClassInSeparateProcess;
}




final public function setPreserveGlobalState(bool $preserveGlobalState): void
{
$this->preserveGlobalState = $preserveGlobalState;
}






final public function setInIsolation(bool $inIsolation): void
{
$this->inIsolation = $inIsolation;
}






final public function result(): mixed
{
return $this->testResult;
}




final public function setResult(mixed $result): void
{
$this->testResult = $result;
}




final public function registerMockObject(MockObject $mockObject): void
{
assert($mockObject instanceof MockObjectInternal);

$this->mockObjects[] = $mockObject;
}




final public function addToAssertionCount(int $count): void
{
$this->numberOfAssertionsPerformed += $count;
}




final public function numberOfAssertionsPerformed(): int
{
return $this->numberOfAssertionsPerformed;
}




final public function usesDataProvider(): bool
{
return !empty($this->data);
}




final public function dataName(): int|string
{
return $this->dataName;
}




final public function dataSetAsString(): string
{
$buffer = '';

if (!empty($this->data)) {
if (is_int($this->dataName)) {
$buffer .= sprintf(' with data set #%d', $this->dataName);
} else {
$buffer .= sprintf(' with data set "%s"', $this->dataName);
}
}

return $buffer;
}




final public function dataSetAsStringWithData(): string
{
if (empty($this->data)) {
return '';
}

return $this->dataSetAsString() . sprintf(
' (%s)',
Exporter::shortenedRecursiveExport($this->data),
);
}






final public function providedData(): array
{
return $this->data;
}




final public function sortId(): string
{
$id = $this->methodName;

if (!str_contains($id, '::')) {
$id = static::class . '::' . $id;
}

if ($this->usesDataProvider()) {
$id .= $this->dataSetAsString();
}

return $id;
}






final public function provides(): array
{
return $this->providedTests;
}






final public function requires(): array
{
return $this->dependencies;
}






final public function setData(int|string $dataName, array $data): void
{
$this->dataName = $dataName;
$this->data = $data;
}




final public function valueObjectForEvents(): Event\Code\TestMethod
{
if ($this->testValueObjectForEvents !== null) {
return $this->testValueObjectForEvents;
}

$this->testValueObjectForEvents = Event\Code\TestMethodBuilder::fromTestCase($this);

return $this->testValueObjectForEvents;
}




final public function wasPrepared(): bool
{
return $this->wasPrepared;
}





final protected function any(): AnyInvokedCountMatcher
{
return new AnyInvokedCountMatcher;
}




final protected function never(): InvokedCountMatcher
{
return new InvokedCountMatcher(0);
}





final protected function atLeast(int $requiredInvocations): InvokedAtLeastCountMatcher
{
return new InvokedAtLeastCountMatcher(
$requiredInvocations,
);
}




final protected function atLeastOnce(): InvokedAtLeastOnceMatcher
{
return new InvokedAtLeastOnceMatcher;
}




final protected function once(): InvokedCountMatcher
{
return new InvokedCountMatcher(1);
}





final protected function exactly(int $count): InvokedCountMatcher
{
return new InvokedCountMatcher($count);
}





final protected function atMost(int $allowedInvocations): InvokedAtMostCountMatcher
{
return new InvokedAtMostCountMatcher($allowedInvocations);
}







final protected function returnValue(mixed $value): ReturnStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'returnValue() is deprecated and will be removed in PHPUnit 12. Use $double->willReturn() instead of $double->will($this->returnValue())',
);

return new ReturnStub($value);
}









final protected function returnValueMap(array $valueMap): ReturnValueMapStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'returnValueMap() is deprecated and will be removed in PHPUnit 12. Use $double->willReturnMap() instead of $double->will($this->returnValueMap())',
);

return new ReturnValueMapStub($valueMap);
}







final protected function returnArgument(int $argumentIndex): ReturnArgumentStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'returnArgument() is deprecated and will be removed in PHPUnit 12. Use $double->willReturnArgument() instead of $double->will($this->returnArgument())',
);

return new ReturnArgumentStub($argumentIndex);
}







final protected function returnCallback(callable $callback): ReturnCallbackStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'returnCallback() is deprecated and will be removed in PHPUnit 12. Use $double->willReturnCallback() instead of $double->will($this->returnCallback())',
);

return new ReturnCallbackStub($callback);
}







final protected function returnSelf(): ReturnSelfStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'returnSelf() is deprecated and will be removed in PHPUnit 12. Use $double->willReturnSelf() instead of $double->will($this->returnSelf())',
);

return new ReturnSelfStub;
}

final protected function throwException(Throwable $exception): ExceptionStub
{
return new ExceptionStub($exception);
}








final protected function onConsecutiveCalls(mixed ...$arguments): ConsecutiveCallsStub
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'onConsecutiveCalls() is deprecated and will be removed in PHPUnit 12. Use $double->willReturn() instead of $double->will($this->onConsecutiveCalls())',
);

return new ConsecutiveCallsStub($arguments);
}

final protected function getActualOutputForAssertion(): string
{
$this->outputRetrievedForAssertion = true;

return $this->output();
}

final protected function expectOutputRegex(string $expectedRegex): void
{
$this->outputExpectedRegex = $expectedRegex;
}

final protected function expectOutputString(string $expectedString): void
{
$this->outputExpectedString = $expectedString;
}




final protected function expectException(string $exception): void
{
$this->expectedException = $exception;
}

final protected function expectExceptionCode(int|string $code): void
{
$this->expectedExceptionCode = $code;
}

final protected function expectExceptionMessage(string $message): void
{
$this->expectedExceptionMessage = $message;
}

final protected function expectExceptionMessageMatches(string $regularExpression): void
{
$this->expectedExceptionMessageRegExp = $regularExpression;
}






final protected function expectExceptionObject(\Exception $exception): void
{
$this->expectException($exception::class);
$this->expectExceptionMessage($exception->getMessage());
$this->expectExceptionCode($exception->getCode());
}

final protected function expectNotToPerformAssertions(): void
{
$this->doesNotPerformAssertions = true;
}




final protected function expectUserDeprecationMessage(string $expectedUserDeprecationMessage): void
{
$this->expectedUserDeprecationMessage[] = $expectedUserDeprecationMessage;
}




final protected function expectUserDeprecationMessageMatches(string $expectedUserDeprecationMessageRegularExpression): void
{
$this->expectedUserDeprecationMessageRegularExpression[] = $expectedUserDeprecationMessageRegularExpression;
}

/**
@template






*/
final protected function getMockBuilder(string $className): MockBuilder
{
return new MockBuilder($this, $className);
}

final protected function registerComparator(Comparator $comparator): void
{
ComparatorFactory::getInstance()->register($comparator);

Event\Facade::emitter()->testRegisteredComparator($comparator::class);

$this->customComparators[] = $comparator;
}




final protected function registerFailureType(string $classOrInterface): void
{
$this->failureTypes[$classOrInterface] = true;
}









final protected function runTest(): mixed
{
$testArguments = array_merge($this->data, array_values($this->dependencyInput));

try {
$testResult = $this->{$this->methodName}(...$testArguments);
} catch (Throwable $exception) {
if (!$this->shouldExceptionExpectationsBeVerified($exception)) {
throw $exception;
}

$this->verifyExceptionExpectations($exception);

return null;
}

$this->expectedExceptionWasNotRaised();

return $testResult;
}












final protected function iniSet(string $varName, string $newValue): void
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'iniSet() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$currentValue = ini_set($varName, $newValue);

if ($currentValue !== false) {
$this->iniSettings[$varName] = $currentValue;
} else {
throw new Exception(
sprintf(
'INI setting "%s" could not be set to "%s".',
$varName,
$newValue,
),
);
}
}











final protected function setLocale(mixed ...$arguments): void
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'setLocale() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

if (count($arguments) < 2) {
throw new Exception;
}

[$category, $locale] = $arguments;

if (!in_array($category, self::LOCALE_CATEGORIES, true)) {
throw new Exception;
}

if (!is_array($locale) && !is_string($locale)) {
throw new Exception;
}

$this->locale[$category] = setlocale($category, '0');

$result = setlocale(...$arguments);

if ($result === false) {
throw new Exception(
'The locale functionality is not implemented on your platform, ' .
'the specified locale does not exist or the category name is ' .
'invalid.',
);
}
}

/**
@template










*/
final protected function createMock(string $originalClassName): MockObject
{
$mock = (new MockGenerator)->testDouble(
$originalClassName,
true,
true,
callOriginalConstructor: false,
callOriginalClone: false,
cloneArguments: false,
allowMockingUnknownTypes: false,
returnValueGeneration: self::generateReturnValuesForTestDoubles(),
);

assert($mock instanceof $originalClassName);
assert($mock instanceof MockObject);

$this->registerMockObject($mock);

Event\Facade::emitter()->testCreatedMockObject($originalClassName);

return $mock;
}






final protected function createMockForIntersectionOfInterfaces(array $interfaces): MockObject
{
$mock = (new MockGenerator)->testDoubleForInterfaceIntersection(
$interfaces,
true,
returnValueGeneration: self::generateReturnValuesForTestDoubles(),
);

assert($mock instanceof MockObject);

$this->registerMockObject($mock);

Event\Facade::emitter()->testCreatedMockObjectForIntersectionOfInterfaces($interfaces);

return $mock;
}

/**
@template











*/
final protected function createConfiguredMock(string $originalClassName, array $configuration): MockObject
{
$o = $this->createMock($originalClassName);

foreach ($configuration as $method => $return) {
$o->method($method)->willReturn($return);
}

return $o;
}

/**
@template










*/
final protected function createPartialMock(string $originalClassName, array $methods): MockObject
{
$mockBuilder = $this->getMockBuilder($originalClassName)
->disableOriginalConstructor()
->disableOriginalClone()
->disableArgumentCloning()
->disallowMockingUnknownTypes()
->onlyMethods($methods);

if (!self::generateReturnValuesForTestDoubles()) {
$mockBuilder->disableAutoReturnValueGeneration();
}

$partialMock = $mockBuilder->getMock();

Event\Facade::emitter()->testCreatedPartialMockObject(
$originalClassName,
...$methods,
);

return $partialMock;
}

/**
@template












*/
final protected function createTestProxy(string $originalClassName, array $constructorArguments = []): MockObject
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'createTestProxy() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$testProxy = $this->getMockBuilder($originalClassName)
->setConstructorArgs($constructorArguments)
->enableProxyingToOriginalMethods()
->getMock();

Event\Facade::emitter()->testCreatedTestProxy(
$originalClassName,
$constructorArguments,
);

return $testProxy;
}

/**
@template















*/
final protected function getMockForAbstractClass(string $originalClassName, array $arguments = [], string $mockClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true, array $mockedMethods = [], bool $cloneArguments = false): MockObject
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'getMockForAbstractClass() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$mockObject = (new MockGenerator)->mockObjectForAbstractClass(
$originalClassName,
$arguments,
$mockClassName,
$callOriginalConstructor,
$callOriginalClone,
$callAutoload,
$mockedMethods,
$cloneArguments,
);

$this->registerMockObject($mockObject);

Event\Facade::emitter()->testCreatedMockObjectForAbstractClass($originalClassName);

assert($mockObject instanceof $originalClassName);
assert($mockObject instanceof MockObject);

return $mockObject;
}











final protected function getMockFromWsdl(string $wsdlFile, string $originalClassName = '', string $mockClassName = '', array $methods = [], bool $callOriginalConstructor = true, array $options = []): MockObject
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'getMockFromWsdl() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

if ($originalClassName === '') {
$fileName = pathinfo(basename(parse_url($wsdlFile, PHP_URL_PATH)), PATHINFO_FILENAME);
$originalClassName = preg_replace('/\W/', '', $fileName);
}

if (!class_exists($originalClassName)) {
eval(
(new MockGenerator)->generateClassFromWsdl(
$wsdlFile,
$originalClassName,
$methods,
$options,
)
);
}

$mockObject = (new MockGenerator)->testDouble(
$originalClassName,
true,
true,
$methods,
['', $options],
$mockClassName,
$callOriginalConstructor,
false,
false,
);

Event\Facade::emitter()->testCreatedMockObjectFromWsdl(
$wsdlFile,
$originalClassName,
$mockClassName,
$methods,
$callOriginalConstructor,
$options,
);

assert($mockObject instanceof MockObject);

$this->registerMockObject($mockObject);

return $mockObject;
}















final protected function getMockForTrait(string $traitName, array $arguments = [], string $mockClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true, array $mockedMethods = [], bool $cloneArguments = false): MockObject
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'getMockForTrait() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

$mockObject = (new MockGenerator)->mockObjectForTrait(
$traitName,
$arguments,
$mockClassName,
$callOriginalConstructor,
$callOriginalClone,
$callAutoload,
$mockedMethods,
$cloneArguments,
);

$this->registerMockObject($mockObject);

Event\Facade::emitter()->testCreatedMockObjectForTrait($traitName);

return $mockObject;
}











final protected function getObjectForTrait(string $traitName, array $arguments = [], string $traitClassName = '', bool $callOriginalConstructor = true, bool $callOriginalClone = true, bool $callAutoload = true): object
{
Event\Facade::emitter()->testTriggeredPhpunitDeprecation(
$this->valueObjectForEvents(),
'getObjectForTrait() is deprecated and will be removed in PHPUnit 12 without replacement.',
);

return (new MockGenerator)->objectForTrait(
$traitName,
$traitClassName,
$callAutoload,
$callOriginalConstructor,
$arguments,
);
}

protected function transformException(Throwable $t): Throwable
{
return $t;
}






protected function onNotSuccessfulTest(Throwable $t): never
{
throw $t;
}




private function verifyDeprecationExpectations(): void
{
foreach ($this->expectedUserDeprecationMessage as $deprecationExpectation) {
$this->numberOfAssertionsPerformed++;

if (!in_array($deprecationExpectation, DeprecationCollector::deprecations(), true)) {
throw new ExpectationFailedException(
sprintf(
'Expected deprecation with message "%s" was not triggered',
$deprecationExpectation,
),
);
}
}

foreach ($this->expectedUserDeprecationMessageRegularExpression as $deprecationExpectation) {
$this->numberOfAssertionsPerformed++;

$expectedDeprecationTriggered = false;

foreach (DeprecationCollector::deprecations() as $deprecation) {
if (@preg_match($deprecationExpectation, $deprecation) > 0) {
$expectedDeprecationTriggered = true;

break;
}
}

if (!$expectedDeprecationTriggered) {
throw new ExpectationFailedException(
sprintf(
'Expected deprecation with message matching regular expression "%s" was not triggered',
$deprecationExpectation,
),
);
}
}
}




private function verifyMockObjects(): void
{
foreach ($this->mockObjects as $mockObject) {
if ($mockObject->__phpunit_hasMatchers()) {
$this->numberOfAssertionsPerformed++;
}

$mockObject->__phpunit_verify(
$this->shouldInvocationMockerBeReset($mockObject),
);
}
}




private function checkRequirements(): void
{
if (!$this->methodName || !method_exists($this, $this->methodName)) {
return;
}

$missingRequirements = (new Requirements)->requirementsNotSatisfiedFor(
static::class,
$this->methodName,
);

if (!empty($missingRequirements)) {
$this->markTestSkipped(implode(PHP_EOL, $missingRequirements));
}
}

private function handleDependencies(): bool
{
if ([] === $this->dependencies || $this->inIsolation) {
return true;
}

$passedTests = PassedTests::instance();

foreach ($this->dependencies as $dependency) {
if (!$dependency->isValid()) {
$this->markErrorForInvalidDependency();

return false;
}

if ($dependency->targetIsClass()) {
$dependencyClassName = $dependency->getTargetClassName();

if (!class_exists($dependencyClassName)) {
$this->markErrorForInvalidDependency($dependency);

return false;
}

if (!$passedTests->hasTestClassPassed($dependencyClassName)) {
$this->markSkippedForMissingDependency($dependency);

return false;
}

continue;
}

$dependencyTarget = $dependency->getTarget();

if (!$passedTests->hasTestMethodPassed($dependencyTarget)) {
if (!$this->isCallableTestMethod($dependencyTarget)) {
$this->markErrorForInvalidDependency($dependency);
} else {
$this->markSkippedForMissingDependency($dependency);
}

return false;
}

if ($passedTests->isGreaterThan($dependencyTarget, $this->size())) {
Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
'This test depends on a test that is larger than itself',
);

return true;
}

$returnValue = $passedTests->returnValue($dependencyTarget);

if ($dependency->deepClone()) {
$deepCopy = new DeepCopy;
$deepCopy->skipUncloneable(false);

$this->dependencyInput[$dependencyTarget] = $deepCopy->copy($returnValue);
} elseif ($dependency->shallowClone()) {
$this->dependencyInput[$dependencyTarget] = clone $returnValue;
} else {
$this->dependencyInput[$dependencyTarget] = $returnValue;
}
}

$this->testValueObjectForEvents = null;

return true;
}





private function markErrorForInvalidDependency(?ExecutionOrderDependency $dependency = null): void
{
$message = 'This test has an invalid dependency';

if ($dependency !== null) {
$message = sprintf(
'This test depends on "%s" which does not exist',
$dependency->targetIsClass() ? $dependency->getTargetClassName() : $dependency->getTarget(),
);
}

$exception = new InvalidDependencyException($message);

Event\Facade::emitter()->testErrored(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($exception),
);

$this->status = TestStatus::error($message);
}

private function markSkippedForMissingDependency(ExecutionOrderDependency $dependency): void
{
$message = sprintf(
'This test depends on "%s" to pass',
$dependency->getTarget(),
);

Event\Facade::emitter()->testSkipped(
$this->valueObjectForEvents(),
$message,
);

$this->status = TestStatus::skipped($message);
}

private function startOutputBuffering(): void
{
ob_start();

$this->outputBufferingActive = true;
$this->outputBufferingLevel = ob_get_level();
}

private function stopOutputBuffering(): bool
{
$bufferingLevel = ob_get_level();

if ($bufferingLevel !== $this->outputBufferingLevel) {
if ($bufferingLevel > $this->outputBufferingLevel) {
$message = 'Test code or tested code did not close its own output buffers';
} else {
$message = 'Test code or tested code closed output buffers other than its own';
}

while (ob_get_level() >= $this->outputBufferingLevel) {
ob_end_clean();
}

Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
$message,
);

return false;
}

$this->output = ob_get_clean();

$this->outputBufferingActive = false;
$this->outputBufferingLevel = ob_get_level();

return true;
}

private function snapshotGlobalErrorExceptionHandlers(): void
{
$this->backupGlobalErrorHandlers = $this->activeErrorHandlers();
$this->backupGlobalExceptionHandlers = $this->activeExceptionHandlers();
}

private function restoreGlobalErrorExceptionHandlers(): void
{
$activeErrorHandlers = $this->activeErrorHandlers();
$activeExceptionHandlers = $this->activeExceptionHandlers();

$message = null;

if ($activeErrorHandlers !== $this->backupGlobalErrorHandlers) {
if (count($activeErrorHandlers) > count($this->backupGlobalErrorHandlers)) {
if (!$this->inIsolation) {
$message = 'Test code or tested code did not remove its own error handlers';
}
} else {
$message = 'Test code or tested code removed error handlers other than its own';
}

foreach ($activeErrorHandlers as $handler) {
restore_error_handler();
}

foreach ($this->backupGlobalErrorHandlers as $handler) {
set_error_handler($handler);
}
}

if ($message !== null) {
Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
$message,
);
}

$message = null;

if ($activeExceptionHandlers !== $this->backupGlobalExceptionHandlers) {
if (count($activeExceptionHandlers) > count($this->backupGlobalExceptionHandlers)) {
if (!$this->inIsolation) {
$message = 'Test code or tested code did not remove its own exception handlers';
}
} else {
$message = 'Test code or tested code removed exception handlers other than its own';
}

foreach ($activeExceptionHandlers as $handler) {
restore_exception_handler();
}

foreach ($this->backupGlobalExceptionHandlers as $handler) {
set_exception_handler($handler);
}
}

$this->backupGlobalErrorHandlers = null;
$this->backupGlobalExceptionHandlers = null;

if ($message !== null) {
Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
$message,
);
}
}




private function activeErrorHandlers(): array
{
$activeErrorHandlers = [];

while (true) {
$previousHandler = set_error_handler(static fn () => false);

restore_error_handler();

if ($previousHandler === null) {
break;
}

$activeErrorHandlers[] = $previousHandler;

restore_error_handler();
}

$activeErrorHandlers = array_reverse($activeErrorHandlers);
$invalidErrorHandlerStack = false;

foreach ($activeErrorHandlers as $handler) {
if (!is_callable($handler)) {
$invalidErrorHandlerStack = true;

continue;
}

set_error_handler($handler);
}

if ($invalidErrorHandlerStack) {
$message = 'At least one error handler is not callable outside the scope it was registered in';

Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
$message,
);
}

return $activeErrorHandlers;
}




private function activeExceptionHandlers(): array
{
$res = [];

while (true) {
$previousHandler = set_exception_handler(static fn () => null);
restore_exception_handler();

if ($previousHandler === null) {
break;
}
$res[] = $previousHandler;
restore_exception_handler();
}
$res = array_reverse($res);

foreach ($res as $handler) {
set_exception_handler($handler);
}

return $res;
}

private function snapshotGlobalState(): void
{
if ($this->runTestInSeparateProcess || $this->inIsolation ||
(!$this->backupGlobals && !$this->backupStaticProperties)) {
return;
}

$snapshot = $this->createGlobalStateSnapshot($this->backupGlobals === true);

$this->snapshot = $snapshot;
}

private function restoreGlobalState(): void
{
if (!$this->snapshot instanceof Snapshot) {
return;
}

if (ConfigurationRegistry::get()->beStrictAboutChangesToGlobalState()) {
$this->compareGlobalStateSnapshots(
$this->snapshot,
$this->createGlobalStateSnapshot($this->backupGlobals === true),
);
}

$restorer = new Restorer;

if ($this->backupGlobals) {
$restorer->restoreGlobalVariables($this->snapshot);
}

if ($this->backupStaticProperties) {
$restorer->restoreStaticProperties($this->snapshot);
}

$this->snapshot = null;
}

private function createGlobalStateSnapshot(bool $backupGlobals): Snapshot
{
$excludeList = new GlobalStateExcludeList;

foreach ($this->backupGlobalsExcludeList as $globalVariable) {
$excludeList->addGlobalVariable($globalVariable);
}

if (!defined('PHPUNIT_TESTSUITE')) {
$excludeList->addClassNamePrefix('PHPUnit');
$excludeList->addClassNamePrefix('SebastianBergmann\CodeCoverage');
$excludeList->addClassNamePrefix('SebastianBergmann\FileIterator');
$excludeList->addClassNamePrefix('SebastianBergmann\Invoker');
$excludeList->addClassNamePrefix('SebastianBergmann\Template');
$excludeList->addClassNamePrefix('SebastianBergmann\Timer');
$excludeList->addStaticProperty(ComparatorFactory::class, 'instance');

foreach ($this->backupStaticPropertiesExcludeList as $class => $properties) {
foreach ($properties as $property) {
$excludeList->addStaticProperty($class, $property);
}
}
}

return new Snapshot(
$excludeList,
$backupGlobals,
(bool) $this->backupStaticProperties,
false,
false,
false,
false,
false,
false,
false,
);
}

private function compareGlobalStateSnapshots(Snapshot $before, Snapshot $after): void
{
$backupGlobals = $this->backupGlobals === null || $this->backupGlobals;

if ($backupGlobals) {
$this->compareGlobalStateSnapshotPart(
$before->globalVariables(),
$after->globalVariables(),
"--- Global variables before the test\n+++ Global variables after the test\n",
);

$this->compareGlobalStateSnapshotPart(
$before->superGlobalVariables(),
$after->superGlobalVariables(),
"--- Super-global variables before the test\n+++ Super-global variables after the test\n",
);
}

if ($this->backupStaticProperties) {
$this->compareGlobalStateSnapshotPart(
$before->staticProperties(),
$after->staticProperties(),
"--- Static properties before the test\n+++ Static properties after the test\n",
);
}
}





private function compareGlobalStateSnapshotPart(array $before, array $after, string $header): void
{
if ($before != $after) {
$differ = new Differ(new UnifiedDiffOutputBuilder($header));

Event\Facade::emitter()->testConsideredRisky(
$this->valueObjectForEvents(),
'This test modified global state but was not expected to do so' . PHP_EOL .
trim(
$differ->diff(
Exporter::export($before),
Exporter::export($after),
),
),
);
}
}

private function shouldInvocationMockerBeReset(MockObject $mock): bool
{
$enumerator = new Enumerator;

if (in_array($mock, $enumerator->enumerate($this->dependencyInput), true)) {
return false;
}

if (!is_array($this->testResult) && !is_object($this->testResult)) {
return true;
}

return !in_array($mock, $enumerator->enumerate($this->testResult), true);
}

private function unregisterCustomComparators(): void
{
$factory = ComparatorFactory::getInstance();

foreach ($this->customComparators as $comparator) {
$factory->unregister($comparator);
}

$this->customComparators = [];
}

private function cleanupIniSettings(): void
{
foreach ($this->iniSettings as $varName => $oldValue) {
ini_set($varName, $oldValue);
}

$this->iniSettings = [];
}

private function cleanupLocaleSettings(): void
{
foreach ($this->locale as $category => $locale) {
setlocale($category, $locale);
}

$this->locale = [];
}




private function shouldExceptionExpectationsBeVerified(Throwable $throwable): bool
{
$result = false;

if ($this->expectedException !== null || $this->expectedExceptionCode !== null || $this->expectedExceptionMessage !== null || $this->expectedExceptionMessageRegExp !== null) {
$result = true;
}

if ($throwable instanceof Exception) {
$result = false;
}

if (is_string($this->expectedException)) {
try {
$reflector = new ReflectionClass($this->expectedException);

} catch (ReflectionException $e) {
throw new Exception(
$e->getMessage(),
$e->getCode(),
$e,
);
}


if ($this->expectedException === 'PHPUnit\Framework\Exception' ||
$this->expectedException === '\PHPUnit\Framework\Exception' ||
$reflector->isSubclassOf(Exception::class)) {
$result = true;
}
}

return $result;
}

private function shouldRunInSeparateProcess(): bool
{
if ($this->inIsolation) {
return false;
}

if ($this->runTestInSeparateProcess) {
return true;
}

if ($this->runClassInSeparateProcess) {
return true;
}

return ConfigurationRegistry::get()->processIsolation();
}

private function isCallableTestMethod(string $dependency): bool
{
[$className, $methodName] = explode('::', $dependency);

if (!class_exists($className)) {
return false;
}

$class = new ReflectionClass($className);

if (!$class->isSubclassOf(__CLASS__)) {
return false;
}

if (!$class->hasMethod($methodName)) {
return false;
}

return TestUtil::isTestMethod(
$class->getMethod($methodName),
);
}






private function performAssertionsOnOutput(): void
{
try {
if ($this->outputExpectedRegex !== null) {
$this->assertMatchesRegularExpression($this->outputExpectedRegex, $this->output);
} elseif ($this->outputExpectedString !== null) {
$this->assertSame($this->outputExpectedString, $this->output);
}
} catch (ExpectationFailedException $e) {
$this->status = TestStatus::failure($e->getMessage());

Event\Facade::emitter()->testFailed(
$this->valueObjectForEvents(),
Event\Code\ThrowableBuilder::from($e),
Event\Code\ComparisonFailureBuilder::from($e),
);

throw $e;
}
}








private function invokeBeforeClassHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['beforeClass'],
$emitter,
'beforeFirstTestMethodCalled',
'beforeFirstTestMethodErrored',
'beforeFirstTestMethodFinished',
);
}






private function invokeBeforeTestHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['before'],
$emitter,
'beforeTestMethodCalled',
'beforeTestMethodErrored',
'beforeTestMethodFinished',
);
}






private function invokePreConditionHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['preCondition'],
$emitter,
'preConditionCalled',
'preConditionErrored',
'preConditionFinished',
);
}






private function invokePostConditionHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['postCondition'],
$emitter,
'postConditionCalled',
'postConditionErrored',
'postConditionFinished',
);
}






private function invokeAfterTestHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['after'],
$emitter,
'afterTestMethodCalled',
'afterTestMethodErrored',
'afterTestMethodFinished',
);
}








private function invokeAfterClassHookMethods(array $hookMethods, Event\Emitter $emitter): void
{
$this->invokeHookMethods(
$hookMethods['afterClass'],
$emitter,
'afterLastTestMethodCalled',
'afterLastTestMethodErrored',
'afterLastTestMethodFinished',
);
}








private function invokeHookMethods(HookMethodCollection $hookMethods, Event\Emitter $emitter, string $calledMethod, string $erroredMethod, string $finishedMethod): void
{
$methodsInvoked = [];

foreach ($hookMethods->methodNamesSortedByPriority() as $methodName) {
if ($this->methodDoesNotExistOrIsDeclaredInTestCase($methodName)) {
continue;
}

$methodInvoked = new Event\Code\ClassMethod(
static::class,
$methodName,
);

try {
$this->{$methodName}();
} catch (Throwable $t) {
}

$emitter->{$calledMethod}(
static::class,
$methodInvoked
);

$methodsInvoked[] = $methodInvoked;

if (isset($t)) {
$emitter->{$erroredMethod}(
static::class,
$methodInvoked,
Event\Code\ThrowableBuilder::from($t),
);

break;
}
}

if (!empty($methodsInvoked)) {
$emitter->{$finishedMethod}(
static::class,
...$methodsInvoked
);
}

if (isset($t)) {
throw $t;
}
}




private function methodDoesNotExistOrIsDeclaredInTestCase(string $methodName): bool
{
$reflector = new ReflectionObject($this);

return !$reflector->hasMethod($methodName) ||
$reflector->getMethod($methodName)->getDeclaringClass()->getName() === self::class;
}




private function verifyExceptionExpectations(\Exception|Throwable $exception): void
{
if ($this->expectedException !== null) {
$this->assertThat(
$exception,
new ExceptionConstraint(
$this->expectedException,
),
);
}

if ($this->expectedExceptionMessage !== null) {
$this->assertThat(
$exception->getMessage(),
new ExceptionMessageIsOrContains(
$this->expectedExceptionMessage,
),
);
}

if ($this->expectedExceptionMessageRegExp !== null) {
$this->assertThat(
$exception->getMessage(),
new ExceptionMessageMatchesRegularExpression(
$this->expectedExceptionMessageRegExp,
),
);
}

if ($this->expectedExceptionCode !== null) {
$this->assertThat(
$exception->getCode(),
new ExceptionCode(
$this->expectedExceptionCode,
),
);
}
}




private function expectedExceptionWasNotRaised(): void
{
if ($this->expectedException !== null) {
$this->assertThat(
null,
new ExceptionConstraint($this->expectedException),
);
} elseif ($this->expectedExceptionMessage !== null) {
$this->numberOfAssertionsPerformed++;

throw new AssertionFailedError(
sprintf(
'Failed asserting that exception with message "%s" is thrown',
$this->expectedExceptionMessage,
),
);
} elseif ($this->expectedExceptionMessageRegExp !== null) {
$this->numberOfAssertionsPerformed++;

throw new AssertionFailedError(
sprintf(
'Failed asserting that exception with message matching "%s" is thrown',
$this->expectedExceptionMessageRegExp,
),
);
} elseif ($this->expectedExceptionCode !== null) {
$this->numberOfAssertionsPerformed++;

throw new AssertionFailedError(
sprintf(
'Failed asserting that exception with code "%s" is thrown',
$this->expectedExceptionCode,
),
);
}
}

private function isRegisteredFailure(Throwable $t): bool
{
foreach (array_keys($this->failureTypes) as $failureType) {
if ($t instanceof $failureType) {
return true;
}
}

return false;
}




private function hasExpectationOnOutput(): bool
{
return is_string($this->outputExpectedString) || is_string($this->outputExpectedRegex);
}

private function requirementsNotSatisfied(): bool
{
return (new Requirements)->requirementsNotSatisfiedFor(static::class, $this->methodName) !== [];
}




private function handleExceptionFromInvokedCountMockObjectRule(Throwable $t): void
{
if (!$t instanceof ExpectationFailedException) {
return;
}

$trace = $t->getTrace();

if (isset($trace[0]['class']) && $trace[0]['class'] === InvokedCount::class) {
$this->numberOfAssertionsPerformed++;
}
}

/**
@template










*/
final protected static function createStub(string $originalClassName): Stub
{
$stub = (new MockGenerator)->testDouble(
$originalClassName,
true,
false,
callOriginalConstructor: false,
callOriginalClone: false,
cloneArguments: false,
allowMockingUnknownTypes: false,
returnValueGeneration: self::generateReturnValuesForTestDoubles(),
);

Event\Facade::emitter()->testCreatedStub($originalClassName);

assert($stub instanceof $originalClassName);
assert($stub instanceof Stub);

return $stub;
}






final protected static function createStubForIntersectionOfInterfaces(array $interfaces): Stub
{
$stub = (new MockGenerator)->testDoubleForInterfaceIntersection(
$interfaces,
false,
returnValueGeneration: self::generateReturnValuesForTestDoubles(),
);

Event\Facade::emitter()->testCreatedStubForIntersectionOfInterfaces($interfaces);

return $stub;
}

/**
@template











*/
final protected static function createConfiguredStub(string $originalClassName, array $configuration): Stub
{
$o = self::createStub($originalClassName);

foreach ($configuration as $method => $return) {
$o->method($method)->willReturn($return);
}

return $o;
}

private static function generateReturnValuesForTestDoubles(): bool
{
return MetadataRegistry::parser()->forClass(static::class)->isDisableReturnValueGenerationForTestDoubles()->isEmpty();
}
}
