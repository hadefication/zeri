<?php

declare(strict_types=1);

namespace Pest\PendingCalls;

use Closure;
use Pest\Concerns\Testable;
use Pest\Exceptions\InvalidArgumentException;
use Pest\Exceptions\TestDescriptionMissing;
use Pest\Factories\Attribute;
use Pest\Factories\TestCaseMethodFactory;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\PendingCalls\Concerns\Describable;
use Pest\Plugins\Only;
use Pest\Support\Backtrace;
use Pest\Support\Container;
use Pest\Support\Exporter;
use Pest\Support\HigherOrderCallables;
use Pest\Support\NullClosure;
use Pest\Support\Str;
use Pest\TestSuite;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

/**
@mixin


*/
final class TestCall 
{
use Describable;






private array $testCaseFactoryAttributes = [];




public readonly TestCaseMethodFactory $testCaseMethod;




private readonly bool $descriptionLess;




public function __construct(
private readonly TestSuite $testSuite,
private readonly string $filename,
private ?string $description = null,
?Closure $closure = null
) {
$this->testCaseMethod = new TestCaseMethodFactory($filename, $closure);

$this->descriptionLess = $description === null;

$this->describing = DescribeCall::describing();

$this->testSuite->beforeEach->get($this->filename)[0]($this);
}




public function after(Closure $closure): self
{
if ($this->description === null) {
throw new TestDescriptionMissing($this->filename);
}

$description = $this->describing === []
? $this->description
: Str::describe($this->describing, $this->description);

$filename = $this->filename;

$when = function () use ($closure, $filename, $description): void {
if ($this::$__filename !== $filename) { 
return;
}

if ($this->__description !== $description) { 
return;
}

if ($this->__ran !== true) { 
return;
}

$closure->call($this);
};

new AfterEachCall($this->testSuite, $this->filename, $when->bindTo(new \stdClass));

return $this;
}




public function fails(?string $message = null): self
{
return $this->throws(AssertionFailedError::class, $message);
}




public function throws(string|int $exception, ?string $exceptionMessage = null, ?int $exceptionCode = null): self
{
if (is_int($exception)) {
$exceptionCode = $exception;
} elseif (class_exists($exception)) {
$this->testCaseMethod
->proxies
->add(Backtrace::file(), Backtrace::line(), 'expectException', [$exception]);
} else {
$exceptionMessage = $exception;
}

if (is_string($exceptionMessage)) {
$this->testCaseMethod
->proxies
->add(Backtrace::file(), Backtrace::line(), 'expectExceptionMessage', [$exceptionMessage]);
}

if (is_int($exceptionCode)) {
$this->testCaseMethod
->proxies
->add(Backtrace::file(), Backtrace::line(), 'expectExceptionCode', [$exceptionCode]);
}

return $this;
}






public function throwsIf(callable|bool $condition, string|int $exception, ?string $exceptionMessage = null, ?int $exceptionCode = null): self
{
$condition = is_callable($condition)
? $condition
: static fn (): bool => $condition;

if ($condition()) {
return $this->throws($exception, $exceptionMessage, $exceptionCode);
}

return $this;
}






public function throwsUnless(callable|bool $condition, string|int $exception, ?string $exceptionMessage = null, ?int $exceptionCode = null): self
{
$condition = is_callable($condition)
? $condition
: static fn (): bool => $condition;

if (! $condition()) {
return $this->throws($exception, $exceptionMessage, $exceptionCode);
}

return $this;
}







public function with(Closure|iterable|string ...$data): self
{
foreach ($data as $dataset) {
$this->testCaseMethod->datasets[] = $dataset;
}

return $this;
}




public function depends(string ...$depends): self
{
foreach ($depends as $depend) {
$this->testCaseMethod->depends[] = $depend;
}

return $this;
}




public function group(string ...$groups): self
{
foreach ($groups as $group) {
$this->testCaseMethod->attributes[] = new Attribute(
\PHPUnit\Framework\Attributes\Group::class,
[$group],
);
}

return $this;
}




public function only(): self
{
Only::enable($this, ...func_get_args());

return $this;
}




public function skip(Closure|bool|string $conditionOrMessage = true, string $message = ''): self
{
$condition = is_string($conditionOrMessage)
? NullClosure::create()
: $conditionOrMessage;

$condition = is_callable($condition)
? $condition
: fn (): bool => $condition;

$message = is_string($conditionOrMessage)
? $conditionOrMessage
: $message;


$condition = $condition->bindTo(null);

$this->testCaseMethod
->chains
->addWhen($condition, $this->filename, Backtrace::line(), 'markTestSkipped', [$message]);

return $this;
}




public function skipOnPhp(string $version): self
{
if (mb_strlen($version) < 2) {
throw new InvalidArgumentException('The version must start with [<] or [>].');
}

if (str_starts_with($version, '>=') || str_starts_with($version, '<=')) {
$operator = substr($version, 0, 2);
$version = substr($version, 2);
} elseif (str_starts_with($version, '>') || str_starts_with($version, '<')) {
$operator = $version[0];
$version = substr($version, 1);

} elseif (is_numeric($version[0])) {
$operator = '==';
} else {
throw new InvalidArgumentException('The version must start with [<, >, <=, >=] or a number.');
}

return $this->skip(version_compare(PHP_VERSION, $version, $operator), sprintf('This test is skipped on PHP [%s%s].', $operator, $version));
}




public function skipOnWindows(): self
{
return $this->skipOnOs('Windows', 'This test is skipped on [Windows].');
}




public function skipOnMac(): self
{
return $this->skipOnOs('Darwin', 'This test is skipped on [Mac].');
}




public function skipOnLinux(): self
{
return $this->skipOnOs('Linux', 'This test is skipped on [Linux].');
}




private function skipOnOs(string $osFamily, string $message): self
{
return $osFamily === PHP_OS_FAMILY
? $this->skip($message)
: $this;
}




public function onlyOnWindows(): self
{
return $this->skipOnMac()->skipOnLinux();
}




public function onlyOnMac(): self
{
return $this->skipOnWindows()->skipOnLinux();
}




public function onlyOnLinux(): self
{
return $this->skipOnWindows()->skipOnMac();
}




public function repeat(int $times): self
{
if ($times < 1) {
throw new InvalidArgumentException('The number of repetitions must be greater than 0.');
}

$this->testCaseMethod->repetitions = $times;

return $this;
}




public function todo(
array|string|null $note = null,
array|string|null $assignee = null,
array|string|int|null $issue = null,
array|string|int|null $pr = null,
): self {
$this->skip('__TODO__');

$this->testCaseMethod->todo = true;

if ($issue !== null) {
$this->issue($issue);
}

if ($pr !== null) {
$this->pr($pr);
}

if ($assignee !== null) {
$this->assignee($assignee);
}

if ($note !== null) {
$this->note($note);
}

return $this;
}




public function wip(
array|string|null $note = null,
array|string|null $assignee = null,
array|string|int|null $issue = null,
array|string|int|null $pr = null,
): self {
if ($issue !== null) {
$this->issue($issue);
}

if ($pr !== null) {
$this->pr($pr);
}

if ($assignee !== null) {
$this->assignee($assignee);
}

if ($note !== null) {
$this->note($note);
}

return $this;
}




public function done(
array|string|null $note = null,
array|string|null $assignee = null,
array|string|int|null $issue = null,
array|string|int|null $pr = null,
): self {
if ($issue !== null) {
$this->issue($issue);
}

if ($pr !== null) {
$this->pr($pr);
}

if ($assignee !== null) {
$this->assignee($assignee);
}

if ($note !== null) {
$this->note($note);
}

return $this;
}






public function issue(array|string|int $number): self
{
$number = is_array($number) ? $number : [$number];

$number = array_map(fn (string|int $number): int => (int) ltrim((string) $number, '#'), $number);

$this->testCaseMethod->issues = array_merge($this->testCaseMethod->issues, $number);

return $this;
}






public function ticket(array|string|int $number): self
{
return $this->issue($number);
}






public function assignee(array|string $assignee): self
{
$assignees = is_array($assignee) ? $assignee : [$assignee];

$this->testCaseMethod->assignees = array_unique(array_merge($this->testCaseMethod->assignees, $assignees));

return $this;
}






public function pr(array|string|int $number): self
{
$number = is_array($number) ? $number : [$number];

$number = array_map(fn (string|int $number): int => (int) ltrim((string) $number, '#'), $number);

$this->testCaseMethod->prs = array_unique(array_merge($this->testCaseMethod->prs, $number));

return $this;
}






public function note(array|string $note): self
{
$notes = is_array($note) ? $note : [$note];

$this->testCaseMethod->notes = array_unique(array_merge($this->testCaseMethod->notes, $notes));

return $this;
}






public function covers(array|string ...$classesOrFunctions): self
{

$classesOrFunctions = array_reduce($classesOrFunctions, fn ($carry, $item): array => is_array($item) ? array_merge($carry, $item) : array_merge($carry, [$item]), []); 

foreach ($classesOrFunctions as $classOrFunction) {
$isClass = class_exists($classOrFunction) || interface_exists($classOrFunction) || enum_exists($classOrFunction);
$isTrait = trait_exists($classOrFunction);
$isFunction = function_exists($classOrFunction);

if (! $isClass && ! $isTrait && ! $isFunction) {
throw new InvalidArgumentException(sprintf('No class, trait or method named "%s" has been found.', $classOrFunction));
}

if ($isClass) {
$this->coversClass($classOrFunction);
} elseif ($isTrait) {
$this->coversTrait($classOrFunction);
} else {
$this->coversFunction($classOrFunction);
}
}

return $this;
}




public function coversClass(string ...$classes): self
{
foreach ($classes as $class) {
$this->testCaseFactoryAttributes[] = new Attribute(
\PHPUnit\Framework\Attributes\CoversClass::class,
[$class],
);
}


$configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);
$paths = $configurationRepository->cliConfiguration->toArray()['paths'] ?? false;

if (! is_array($paths)) {
$configurationRepository->globalConfiguration('default')->class(...$classes); 
}

return $this;
}




public function coversTrait(string ...$traits): self
{
foreach ($traits as $trait) {
$this->testCaseFactoryAttributes[] = new Attribute(
\PHPUnit\Framework\Attributes\CoversTrait::class,
[$trait],
);
}


$configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);
$paths = $configurationRepository->cliConfiguration->toArray()['paths'] ?? false;

if (! is_array($paths)) {
$configurationRepository->globalConfiguration('default')->class(...$traits); 
}

return $this;
}




public function coversFunction(string ...$functions): self
{
foreach ($functions as $function) {
$this->testCaseFactoryAttributes[] = new Attribute(
\PHPUnit\Framework\Attributes\CoversFunction::class,
[$function],
);
}

return $this;
}




public function coversNothing(): self
{
$this->testCaseMethod->attributes[] = new Attribute(
\PHPUnit\Framework\Attributes\CoversNothing::class,
[],
);

return $this;
}






public function throwsNoExceptions(): self
{
$this->testCaseMethod->proxies->add(Backtrace::file(), Backtrace::line(), 'expectNotToPerformAssertions', []);

return $this;
}




public function __get(string $name): self
{
return $this->addChain(Backtrace::file(), Backtrace::line(), $name);
}






public function __call(string $name, array $arguments): self
{
return $this->addChain(Backtrace::file(), Backtrace::line(), $name, $arguments);
}






private function addChain(string $file, int $line, string $name, ?array $arguments = null): self
{
$exporter = Exporter::default();

$this->testCaseMethod
->chains
->add($file, $line, $name, $arguments);

if ($this->descriptionLess) {
Exporter::default();

if ($this->description !== null) {
$this->description .= ' → ';
}

$this->description .= $arguments === null
? $name
: sprintf('%s %s', $name, $exporter->shortenedRecursiveExport($arguments));
}

return $this;
}




public function __destruct()
{
if ($this->description === null) {
throw new TestDescriptionMissing($this->filename);
}

if ($this->describing !== []) {
$this->testCaseMethod->describing = $this->describing;
$this->testCaseMethod->description = Str::describe($this->describing, $this->description);
} else {
$this->testCaseMethod->description = $this->description;
}

$this->testSuite->tests->set($this->testCaseMethod);

if (! is_null($testCase = $this->testSuite->tests->get($this->filename))) {
$testCase->attributes = array_merge($testCase->attributes, $this->testCaseFactoryAttributes);
}
}
}
