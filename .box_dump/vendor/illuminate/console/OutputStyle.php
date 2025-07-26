<?php

namespace Illuminate\Console;

use Illuminate\Console\Contracts\NewLineAware;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class OutputStyle extends SymfonyStyle implements NewLineAware
{





private $output;








protected $newLinesWritten = 1;








protected $newLineWritten = false;







public function __construct(InputInterface $input, OutputInterface $output)
{
$this->output = $output;

parent::__construct($input, $output);
}




#[\Override]
public function askQuestion(Question $question): mixed
{
try {
return parent::askQuestion($question);
} finally {
$this->newLinesWritten++;
}
}




#[\Override]
public function write(string|iterable $messages, bool $newline = false, int $options = 0): void
{
$this->newLinesWritten = $this->trailingNewLineCount($messages) + (int) $newline;
$this->newLineWritten = $this->newLinesWritten > 0;

parent::write($messages, $newline, $options);
}




#[\Override]
public function writeln(string|iterable $messages, int $type = self::OUTPUT_NORMAL): void
{
if ($this->output->getVerbosity() >= $type) {
$this->newLinesWritten = $this->trailingNewLineCount($messages) + 1;
$this->newLineWritten = true;
}

parent::writeln($messages, $type);
}




#[\Override]
public function newLine(int $count = 1): void
{
$this->newLinesWritten += $count;
$this->newLineWritten = $this->newLinesWritten > 0;

parent::newLine($count);
}




public function newLinesWritten()
{
if ($this->output instanceof static) {
return $this->output->newLinesWritten();
}

return $this->newLinesWritten;
}






public function newLineWritten()
{
if ($this->output instanceof static && $this->output->newLineWritten()) {
return true;
}

return $this->newLineWritten;
}







protected function trailingNewLineCount($messages)
{
if (is_iterable($messages)) {
$string = '';

foreach ($messages as $message) {
$string .= $message.PHP_EOL;
}
} else {
$string = $messages;
}

return strlen($string) - strlen(rtrim($string, PHP_EOL));
}






public function isQuiet(): bool
{
return $this->output->isQuiet();
}






public function isVerbose(): bool
{
return $this->output->isVerbose();
}






public function isVeryVerbose(): bool
{
return $this->output->isVeryVerbose();
}






public function isDebug(): bool
{
return $this->output->isDebug();
}






public function getOutput()
{
return $this->output;
}
}
