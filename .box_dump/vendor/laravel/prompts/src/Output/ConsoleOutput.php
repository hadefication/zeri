<?php

namespace Laravel\Prompts\Output;

use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;

class ConsoleOutput extends SymfonyConsoleOutput
{



protected int $newLinesWritten = 1;




public function newLinesWritten(): int
{
return $this->newLinesWritten;
}




protected function doWrite(string $message, bool $newline): void
{
parent::doWrite($message, $newline);

if ($newline) {
$message .= \PHP_EOL;
}

$trailingNewLines = strlen($message) - strlen(rtrim($message, \PHP_EOL));

if (trim($message) === '') {
$this->newLinesWritten += $trailingNewLines;
} else {
$this->newLinesWritten = $trailingNewLines;
}
}




public function writeDirectly(string $message): void
{
parent::doWrite($message, false);
}
}
