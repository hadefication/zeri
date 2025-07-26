<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Concerns\Truncation;
use Laravel\Prompts\Prompt;

abstract class Renderer
{
use Colors;
use Truncation;




protected string $output = '';




public function __construct(protected Prompt $prompt)
{

}




protected function line(string $message): self
{
$this->output .= $message.PHP_EOL;

return $this;
}




protected function newLine(int $count = 1): self
{
$this->output .= str_repeat(PHP_EOL, $count);

return $this;
}




protected function warning(string $message): self
{
return $this->line($this->yellow("  ⚠ {$message}"));
}




protected function error(string $message): self
{
return $this->line($this->red("  ⚠ {$message}"));
}




protected function hint(string $message): self
{
if ($message === '') {
return $this;
}

$message = $this->truncate($message, $this->prompt->terminal()->cols() - 6);

return $this->line($this->gray("  {$message}"));
}






protected function when(mixed $value, callable $callback, ?callable $default = null): self
{
if ($value) {
$callback($this);
} elseif ($default) {
$default($this);
}

return $this;
}




public function __toString()
{
return str_repeat(PHP_EOL, max(2 - $this->prompt->newLinesWritten(), 0))
.$this->output
.(in_array($this->prompt->state, ['submit', 'cancel']) ? PHP_EOL : '');
}
}
