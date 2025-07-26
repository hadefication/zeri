<?php

namespace Illuminate\Testing;

use Illuminate\Console\OutputStyle;
use Illuminate\Console\PromptValidationException;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Laravel\Prompts\Note as PromptsNote;
use Laravel\Prompts\Prompt as BasePrompt;
use Laravel\Prompts\Table as PromptsTable;
use Mockery;
use Mockery\Exception\NoMatchingExpectationException;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Question\ChoiceQuestion;

class PendingCommand
{
use Conditionable, Macroable, Tappable;






public $test;






protected $app;






protected $command;






protected $parameters;






protected $expectedExitCode;






protected $unexpectedExitCode;






protected $hasExecuted = false;









public function __construct(PHPUnitTestCase $test, Container $app, $command, $parameters)
{
$this->app = $app;
$this->test = $test;
$this->command = $command;
$this->parameters = $parameters;
}








public function expectsQuestion($question, $answer)
{
$this->test->expectedQuestions[] = [$question, $answer];

return $this;
}








public function expectsConfirmation($question, $answer = 'no')
{
return $this->expectsQuestion($question, strtolower($answer) === 'yes');
}










public function expectsChoice($question, $answer, $answers, $strict = false)
{
$this->test->expectedChoices[$question] = [
'expected' => $answers,
'strict' => $strict,
];

return $this->expectsQuestion($question, $answer);
}










public function expectsSearch($question, $answer, $search, $answers)
{
return $this
->expectsQuestion($question, $search)
->expectsChoice($question, $answer, $answers);
}







public function expectsOutput($output = null)
{
if ($output === null) {
$this->test->expectsOutput = true;

return $this;
}

$this->test->expectedOutput[] = $output;

return $this;
}







public function doesntExpectOutput($output = null)
{
if ($output === null) {
$this->test->expectsOutput = false;

return $this;
}

$this->test->unexpectedOutput[$output] = false;

return $this;
}







public function expectsOutputToContain($string)
{
$this->test->expectedOutputSubstrings[] = $string;

return $this;
}







public function doesntExpectOutputToContain($string)
{
$this->test->unexpectedOutputSubstrings[$string] = false;

return $this;
}










public function expectsTable($headers, $rows, $tableStyle = 'default', array $columnStyles = [])
{
$table = (new Table($output = new BufferedOutput))
->setHeaders((array) $headers)
->setRows($rows instanceof Arrayable ? $rows->toArray() : $rows)
->setStyle($tableStyle);

foreach ($columnStyles as $columnIndex => $columnStyle) {
$table->setColumnStyle($columnIndex, $columnStyle);
}

$table->render();

$lines = array_filter(
explode(PHP_EOL, $output->fetch())
);

foreach ($lines as $line) {
$this->expectsOutput($line);
}

return $this;
}






public function expectsPromptsInfo(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'info')
);

return $this;
}






public function expectsPromptsWarning(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'warning')
);

return $this;
}






public function expectsPromptsError(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'error')
);

return $this;
}






public function expectsPromptsAlert(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'alert')
);

return $this;
}






public function expectsPromptsIntro(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'intro')
);

return $this;
}






public function expectsPromptsOutro(string $message)
{
$this->expectOutputToContainPrompt(
new PromptsNote($message, 'outro')
);

return $this;
}

/**
@phpstan-param($rows is null ? list<list<string>>|Collection<int, list<string>> : list<string|list<string>>|Collection<int, string|list<string>>) $headers






*/
public function expectsPromptsTable(array|Collection $headers, array|Collection|null $rows)
{
$this->expectOutputToContainPrompt(
new PromptsTable($headers, $rows)
);

return $this;
}






protected function expectOutputToContainPrompt(BasePrompt $prompt)
{
$prompt->setOutput($output = new BufferedOutput);

$prompt->display();

$this->expectsOutputToContain(trim($output->fetch()));
}







public function assertExitCode($exitCode)
{
$this->expectedExitCode = $exitCode;

return $this;
}







public function assertNotExitCode($exitCode)
{
$this->unexpectedExitCode = $exitCode;

return $this;
}






public function assertSuccessful()
{
return $this->assertExitCode(Command::SUCCESS);
}






public function assertOk()
{
return $this->assertSuccessful();
}






public function assertFailed()
{
return $this->assertNotExitCode(Command::SUCCESS);
}






public function execute()
{
return $this->run();
}








public function run()
{
$this->hasExecuted = true;

$mock = $this->mockConsoleOutput();

try {
$exitCode = $this->app->make(Kernel::class)->call($this->command, $this->parameters, $mock);
} catch (NoMatchingExpectationException $e) {
if ($e->getMethodName() === 'askQuestion') {
$this->test->fail('Unexpected question "'.$e->getActualArguments()[0]->getQuestion().'" was asked.');
}

throw $e;
} catch (PromptValidationException) {
$exitCode = Command::FAILURE;
}

if ($this->expectedExitCode !== null) {
$this->test->assertEquals(
$this->expectedExitCode, $exitCode,
"Expected status code {$this->expectedExitCode} but received {$exitCode}."
);
} elseif (! is_null($this->unexpectedExitCode)) {
$this->test->assertNotEquals(
$this->unexpectedExitCode, $exitCode,
"Unexpected status code {$this->unexpectedExitCode} was received."
);
}

$this->verifyExpectations();
$this->flushExpectations();

$this->app->offsetUnset(OutputStyle::class);

return $exitCode;
}






protected function verifyExpectations()
{
if (count($this->test->expectedQuestions)) {
$this->test->fail('Question "'.Arr::first($this->test->expectedQuestions)[0].'" was not asked.');
}

if (count($this->test->expectedChoices) > 0) {
foreach ($this->test->expectedChoices as $question => $answers) {
$assertion = $answers['strict'] ? 'assertEquals' : 'assertEqualsCanonicalizing';

$this->test->{$assertion}(
$answers['expected'],
$answers['actual'],
'Question "'.$question.'" has different options.'
);
}
}

if (count($this->test->expectedOutput)) {
$this->test->fail('Output "'.Arr::first($this->test->expectedOutput).'" was not printed.');
}

if (count($this->test->expectedOutputSubstrings)) {
$this->test->fail('Output does not contain "'.Arr::first($this->test->expectedOutputSubstrings).'".');
}

if ($output = array_search(true, $this->test->unexpectedOutput)) {
$this->test->fail('Output "'.$output.'" was printed.');
}

if ($output = array_search(true, $this->test->unexpectedOutputSubstrings)) {
$this->test->fail('Output "'.$output.'" was printed.');
}
}






protected function mockConsoleOutput()
{
$mock = Mockery::mock(OutputStyle::class.'[askQuestion]', [
new ArrayInput($this->parameters), $this->createABufferedOutputMock(),
]);

foreach ($this->test->expectedQuestions as $i => $question) {
$mock->shouldReceive('askQuestion')
->once()
->ordered()
->with(Mockery::on(function ($argument) use ($question) {
if (isset($this->test->expectedChoices[$question[0]])) {
$this->test->expectedChoices[$question[0]]['actual'] = $argument instanceof ChoiceQuestion && ! array_is_list($this->test->expectedChoices[$question[0]]['expected'])
? $argument->getChoices()
: $argument->getAutocompleterValues();
}

return $argument->getQuestion() == $question[0];
}))
->andReturnUsing(function () use ($question, $i) {
unset($this->test->expectedQuestions[$i]);

return $question[1];
});
}

$this->app->bind(OutputStyle::class, function () use ($mock) {
return $mock;
});

return $mock;
}






private function createABufferedOutputMock()
{
$mock = Mockery::mock(BufferedOutput::class.'[doWrite]')
->shouldAllowMockingProtectedMethods()
->shouldIgnoreMissing();

if ($this->test->expectsOutput === false) {
$mock->shouldReceive('doWrite')->never();

return $mock;
}

if ($this->test->expectsOutput === true
&& count($this->test->expectedOutput) === 0
&& count($this->test->expectedOutputSubstrings) === 0) {
$mock->shouldReceive('doWrite')->atLeast()->once();
}

foreach ($this->test->expectedOutput as $i => $output) {
$mock->shouldReceive('doWrite')
->once()
->ordered()
->with($output, Mockery::any())
->andReturnUsing(function () use ($i) {
unset($this->test->expectedOutput[$i]);
});
}

foreach ($this->test->expectedOutputSubstrings as $i => $text) {
$mock->shouldReceive('doWrite')
->atLeast()
->times(0)
->withArgs(fn ($output) => str_contains($output, $text))
->andReturnUsing(function () use ($i) {
unset($this->test->expectedOutputSubstrings[$i]);
});
}

foreach ($this->test->unexpectedOutput as $output => $displayed) {
$mock->shouldReceive('doWrite')
->atLeast()
->times(0)
->ordered()
->with($output, Mockery::any())
->andReturnUsing(function () use ($output) {
$this->test->unexpectedOutput[$output] = true;
});
}

foreach ($this->test->unexpectedOutputSubstrings as $text => $displayed) {
$mock->shouldReceive('doWrite')
->atLeast()
->times(0)
->withArgs(fn ($output) => str_contains($output, $text))
->andReturnUsing(function () use ($text) {
$this->test->unexpectedOutputSubstrings[$text] = true;
});
}

return $mock;
}






protected function flushExpectations()
{
$this->test->expectedOutput = [];
$this->test->expectedOutputSubstrings = [];
$this->test->unexpectedOutput = [];
$this->test->unexpectedOutputSubstrings = [];
$this->test->expectedTables = [];
$this->test->expectedQuestions = [];
$this->test->expectedChoices = [];
}






public function __destruct()
{
if ($this->hasExecuted) {
return;
}

$this->run();
}
}
