<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Printers;

use NunoMaduro\Collision\Adapters\Phpunit\ConfigureIO;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use NunoMaduro\Collision\Adapters\Phpunit\Style;
use NunoMaduro\Collision\Adapters\Phpunit\Support\ResultReflection;
use NunoMaduro\Collision\Adapters\Phpunit\TestResult;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Exceptions\TestOutcome;
use Pest\Result;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\ThrowableBuilder;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\NoticeTriggered;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpNoticeTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PrintedUnexpectedOutput;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\TestRunner\DeprecationTriggered as TestRunnerDeprecationTriggered;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\WarningTriggered as TestRunnerWarningTriggered;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedWithMessageException;
use PHPUnit\TestRunner\TestResult\Facade;
use PHPUnit\TextUI\Configuration\Registry;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;




final class DefaultPrinter
{



private ConsoleOutput $output;




private State $state;




private Style $style;




private static bool $compact = false;




private static bool $profile = false;




private array $profileSlowTests = [];




private float $testStartedAt = 0.0;




private static bool $verbose = false;




public function __construct(bool $colors)
{
$this->output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, $colors);

ConfigureIO::of(new ArgvInput, $this->output);

class_exists(\Pest\Collision\Events::class) && \Pest\Collision\Events::setOutput($this->output);

self::$verbose = $this->output->isVerbose();

$this->style = new Style($this->output);

$this->state = new State;
}




public static function compact(?bool $value = null): bool
{
if (! is_null($value)) {
self::$compact = $value;
}

return ! self::$verbose && self::$compact;
}




public static function profile(?bool $value = null): bool
{
if (! is_null($value)) {
self::$profile = $value;
}

return self::$profile;
}




public function setDecorated(bool $decorated): void
{
$this->output->setDecorated($decorated);
}




public function testPrintedUnexpectedOutput(PrintedUnexpectedOutput $printedUnexpectedOutput): void
{
$this->output->write($printedUnexpectedOutput->output());
}




public function testRunnerExecutionStarted(ExecutionStarted $executionStarted): void
{

}




public function testFinished(Finished $event): void
{
$duration = (hrtime(true) - $this->testStartedAt) / 1_000_000;

$test = $event->test();

if (! $test instanceof TestMethod) {
throw new ShouldNotHappen;
}

if (! $this->state->existsInTestCase($event->test())) {
$this->state->add(TestResult::fromTestCase($event->test(), TestResult::PASS));
}

$result = $this->state->setDuration($test, $duration);

if (self::$profile) {
$this->profileSlowTests[$event->test()->id()] = $result;


uasort($this->profileSlowTests, static function (TestResult $a, TestResult $b) {
return $b->duration <=> $a->duration;
});

$this->profileSlowTests = array_slice($this->profileSlowTests, 0, 10);
}
}




public function testPreparationStarted(PreparationStarted $event): void
{
$this->testStartedAt = hrtime(true);

$test = $event->test();

if (! $test instanceof TestMethod) {
throw new ShouldNotHappen;
}

if ($this->state->testCaseHasChanged($test)) {
$this->style->writeCurrentTestCaseSummary($this->state);

$this->state->moveTo($test);
}
}




public function testBeforeFirstTestMethodErrored(BeforeFirstTestMethodErrored $event): void
{
$this->state->add(TestResult::fromBeforeFirstTestMethodErrored($event));
}




public function testErrored(Errored $event): void
{
$this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $event->throwable()));
}




public function testFailed(Failed $event): void
{
$throwable = $event->throwable();

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $throwable));
}




public function testMarkedIncomplete(MarkedIncomplete $event): void
{
$this->state->add(TestResult::fromTestCase($event->test(), TestResult::INCOMPLETE, $event->throwable()));
}




public function testConsideredRisky(ConsideredRisky $event): void
{
$throwable = ThrowableBuilder::from(new IncompleteTestError($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::RISKY, $throwable));
}




public function testRunnerDeprecationTriggered(TestRunnerDeprecationTriggered $event): void
{
$this->style->writeWarning($event->message());
}




public function testRunnerWarningTriggered(TestRunnerWarningTriggered $event): void
{
if (! str_starts_with($event->message(), 'No tests found in class')) {
$this->style->writeWarning($event->message());
}
}




public function testPhpDeprecationTriggered(PhpDeprecationTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
}




public function testPhpNoticeTriggered(PhpNoticeTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::NOTICE, $throwable));
}




public function testPhpWarningTriggered(PhpWarningTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
}




public function testPhpunitWarningTriggered(PhpunitWarningTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
}




public function testDeprecationTriggered(DeprecationTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
}




public function testPhpunitDeprecationTriggered(PhpunitDeprecationTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
}




public function testPhpunitErrorTriggered(PhpunitErrorTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $throwable));
}




public function testNoticeTriggered(NoticeTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::NOTICE, $throwable));
}




public function testWarningTriggered(WarningTriggered $event): void
{
$throwable = ThrowableBuilder::from(new TestOutcome($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::WARN, $throwable));
}




public function testSkipped(Skipped $event): void
{
if ($event->message() === '__TODO__') {
$this->state->add(TestResult::fromTestCase($event->test(), TestResult::TODO));

return;
}

$throwable = ThrowableBuilder::from(new SkippedWithMessageException($event->message()));

$this->state->add(TestResult::fromTestCase($event->test(), TestResult::SKIPPED, $throwable));
}




public function testPassed(Passed $event): void
{
if (! $this->state->existsInTestCase($event->test())) {
$this->state->add(TestResult::fromTestCase($event->test(), TestResult::PASS));
}
}




public function testRunnerExecutionFinished(ExecutionFinished $event): void
{
$result = Facade::result();

if (ResultReflection::numberOfTests(Facade::result()) === 0) {
$this->output->writeln([
'',
'  <fg=white;options=bold;bg=blue> INFO </> No tests found.',
'',
]);

return;
}

$this->style->writeCurrentTestCaseSummary($this->state);

if (self::$compact) {
$this->output->writeln(['']);
}

if (class_exists(Result::class)) {
$failed = Result::failed(Registry::get(), Facade::result());
} else {
$failed = ! Facade::result()->wasSuccessful();
}

$this->style->writeErrorsSummary($this->state);

$this->style->writeRecap($this->state, $event->telemetryInfo(), $result);

if (! $failed && count($this->profileSlowTests) > 0) {
$this->style->writeSlowTests($this->profileSlowTests, $event->telemetryInfo());
}
}




public function report(Throwable $throwable): void
{
$this->style->writeError(ThrowableBuilder::from($throwable));
}
}
