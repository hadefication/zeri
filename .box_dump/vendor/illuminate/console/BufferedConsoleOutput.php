<?php

namespace Illuminate\Console;

use Symfony\Component\Console\Output\ConsoleOutput;

class BufferedConsoleOutput extends ConsoleOutput
{





protected $buffer = '';






public function fetch()
{
return tap($this->buffer, function () {
$this->buffer = '';
});
}




#[\Override]
protected function doWrite(string $message, bool $newline): void
{
$this->buffer .= $message;

if ($newline) {
$this->buffer .= \PHP_EOL;
}

parent::doWrite($message, $newline);
}
}
