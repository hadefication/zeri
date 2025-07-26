<?php declare(strict_types=1);








namespace PHPUnit\TestRunner\TestResult\Issues;

use function array_keys;
use function count;
use PHPUnit\Event\Code\Test;

/**
@no-named-arguments


*/
final class Issue
{



private readonly string $file;




private readonly int $line;




private readonly string $description;




private array $triggeringTests;




private ?string $stackTrace;






public static function from(string $file, int $line, string $description, Test $triggeringTest, ?string $stackTrace = null): self
{
return new self($file, $line, $description, $triggeringTest, $stackTrace);
}






private function __construct(string $file, int $line, string $description, Test $triggeringTest, ?string $stackTrace)
{
$this->file = $file;
$this->line = $line;
$this->description = $description;
$this->stackTrace = $stackTrace;

$this->triggeringTests = [
$triggeringTest->id() => [
'test' => $triggeringTest,
'count' => 1,
],
];
}

public function triggeredBy(Test $test): void
{
if (isset($this->triggeringTests[$test->id()])) {
$this->triggeringTests[$test->id()]['count']++;

return;
}

$this->triggeringTests[$test->id()] = [
'test' => $test,
'count' => 1,
];
}




public function file(): string
{
return $this->file;
}




public function line(): int
{
return $this->line;
}




public function description(): string
{
return $this->description;
}




public function triggeringTests(): array
{
return $this->triggeringTests;
}

/**
@phpstan-assert-if-true
*/
public function hasStackTrace(): bool
{
return $this->stackTrace !== null;
}




public function stackTrace(): ?string
{
return $this->stackTrace;
}

public function triggeredInTest(): bool
{
return count($this->triggeringTests) === 1 &&
$this->file === $this->triggeringTests[array_keys($this->triggeringTests)[0]]['test']->file();
}
}
