<?php declare(strict_types=1);








namespace PHPUnit\Event;

use PHPUnit\Event\Code\ClassMethod;
use PHPUnit\Event\Code\ComparisonFailure;
use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\TestSuite\TestSuite;
use PHPUnit\TextUI\Configuration\Configuration;

/**
@no-named-arguments


*/
interface Emitter
{
public function applicationStarted(): void;

public function testRunnerStarted(): void;

public function testRunnerConfigured(Configuration $configuration): void;

public function testRunnerBootstrapFinished(string $filename): void;

public function testRunnerLoadedExtensionFromPhar(string $filename, string $name, string $version): void;





public function testRunnerBootstrappedExtension(string $className, array $parameters): void;

public function dataProviderMethodCalled(ClassMethod $testMethod, ClassMethod $dataProviderMethod): void;

public function dataProviderMethodFinished(ClassMethod $testMethod, ClassMethod ...$calledMethods): void;

public function testSuiteLoaded(TestSuite $testSuite): void;

public function testSuiteFiltered(TestSuite $testSuite): void;

public function testSuiteSorted(int $executionOrder, int $executionOrderDefects, bool $resolveDependencies): void;

public function testRunnerEventFacadeSealed(): void;

public function testRunnerExecutionStarted(TestSuite $testSuite): void;

public function testRunnerDisabledGarbageCollection(): void;

public function testRunnerTriggeredGarbageCollection(): void;

public function testSuiteSkipped(TestSuite $testSuite, string $message): void;

public function testSuiteStarted(TestSuite $testSuite): void;

public function testPreparationStarted(Code\Test $test): void;

public function testPreparationFailed(Code\Test $test): void;




public function beforeFirstTestMethodCalled(string $testClassName, ClassMethod $calledMethod): void;




public function beforeFirstTestMethodErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function beforeFirstTestMethodFinished(string $testClassName, ClassMethod ...$calledMethods): void;




public function beforeTestMethodCalled(string $testClassName, ClassMethod $calledMethod): void;




public function beforeTestMethodErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function beforeTestMethodFinished(string $testClassName, ClassMethod ...$calledMethods): void;




public function preConditionCalled(string $testClassName, ClassMethod $calledMethod): void;




public function preConditionErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function preConditionFinished(string $testClassName, ClassMethod ...$calledMethods): void;

public function testPrepared(Code\Test $test): void;




public function testRegisteredComparator(string $className): void;




public function testCreatedMockObject(string $className): void;




public function testCreatedMockObjectForIntersectionOfInterfaces(array $interfaces): void;




public function testCreatedMockObjectForTrait(string $traitName): void;




public function testCreatedMockObjectForAbstractClass(string $className): void;







public function testCreatedMockObjectFromWsdl(string $wsdlFile, string $originalClassName, string $mockClassName, array $methods, bool $callOriginalConstructor, array $options): void;




public function testCreatedPartialMockObject(string $className, string ...$methodNames): void;





public function testCreatedTestProxy(string $className, array $constructorArguments): void;




public function testCreatedStub(string $className): void;




public function testCreatedStubForIntersectionOfInterfaces(array $interfaces): void;

public function testErrored(Code\Test $test, Throwable $throwable): void;

public function testFailed(Code\Test $test, Throwable $throwable, ?ComparisonFailure $comparisonFailure): void;

public function testPassed(Code\Test $test): void;




public function testConsideredRisky(Code\Test $test, string $message): void;

public function testMarkedAsIncomplete(Code\Test $test, Throwable $throwable): void;




public function testSkipped(Code\Test $test, string $message): void;




public function testTriggeredPhpunitDeprecation(?Code\Test $test, string $message): void;






public function testTriggeredPhpDeprecation(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline, bool $ignoredByTest, IssueTrigger $trigger): void;







public function testTriggeredDeprecation(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline, bool $ignoredByTest, IssueTrigger $trigger, string $stackTrace): void;






public function testTriggeredError(Code\Test $test, string $message, string $file, int $line, bool $suppressed): void;






public function testTriggeredNotice(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline): void;






public function testTriggeredPhpNotice(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline): void;






public function testTriggeredWarning(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline): void;






public function testTriggeredPhpWarning(Code\Test $test, string $message, string $file, int $line, bool $suppressed, bool $ignoredByBaseline): void;




public function testTriggeredPhpunitError(Code\Test $test, string $message): void;




public function testTriggeredPhpunitWarning(Code\Test $test, string $message): void;




public function testPrintedUnexpectedOutput(string $output): void;

public function testFinished(Code\Test $test, int $numberOfAssertionsPerformed): void;




public function postConditionCalled(string $testClassName, ClassMethod $calledMethod): void;




public function postConditionErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function postConditionFinished(string $testClassName, ClassMethod ...$calledMethods): void;




public function afterTestMethodCalled(string $testClassName, ClassMethod $calledMethod): void;




public function afterTestMethodErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function afterTestMethodFinished(string $testClassName, ClassMethod ...$calledMethods): void;




public function afterLastTestMethodCalled(string $testClassName, ClassMethod $calledMethod): void;




public function afterLastTestMethodErrored(string $testClassName, ClassMethod $calledMethod, Throwable $throwable): void;




public function afterLastTestMethodFinished(string $testClassName, ClassMethod ...$calledMethods): void;

public function testSuiteFinished(TestSuite $testSuite): void;

public function testRunnerStartedChildProcess(): void;

public function testRunnerFinishedChildProcess(string $stdout, string $stderr): void;

public function testRunnerTriggeredPhpunitDeprecation(string $message): void;

public function testRunnerTriggeredPhpunitWarning(string $message): void;

public function testRunnerEnabledGarbageCollection(): void;

public function testRunnerExecutionAborted(): void;

public function testRunnerExecutionFinished(): void;

public function testRunnerFinished(): void;

public function applicationFinished(int $shellExitCode): void;
}
