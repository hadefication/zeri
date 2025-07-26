<?php

namespace Laravel\Prompts\Output;

class BufferedConsoleOutput extends ConsoleOutput
{



protected string $buffer = '';




public function fetch(): string
{
$content = $this->buffer;
$this->buffer = '';

return $content;
}




public function content(): string
{
return $this->buffer;
}




protected function doWrite(string $message, bool $newline): void
{
$this->buffer .= $message;

if ($newline) {
$this->buffer .= \PHP_EOL;
}
}




public function writeDirectly(string $message): void
{
$this->doWrite($message, false);
}
}
