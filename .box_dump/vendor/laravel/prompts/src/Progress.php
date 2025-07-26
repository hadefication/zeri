<?php

namespace Laravel\Prompts;

use Closure;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
@template
*/
class Progress extends Prompt
{



public int $progress = 0;




public int $total = 0;




protected bool $originalAsync;






public function __construct(public string $label, public iterable|int $steps, public string $hint = '')
{
$this->total = match (true) { 
is_int($this->steps) => $this->steps,
is_countable($this->steps) => count($this->steps),
is_iterable($this->steps) => iterator_count($this->steps),
default => throw new InvalidArgumentException('Unable to count steps.'),
};

if ($this->total === 0) {
throw new InvalidArgumentException('Progress bar must have at least one item.');
}
}

/**
@template





*/
public function map(Closure $callback): array
{
$this->start();

$result = [];

try {
if (is_int($this->steps)) {
for ($i = 0; $i < $this->steps; $i++) {
$result[] = $callback($i, $this);
$this->advance();
}
} else {
foreach ($this->steps as $step) {
$result[] = $callback($step, $this);
$this->advance();
}
}
} catch (Throwable $e) {
$this->state = 'error';
$this->render();
$this->restoreCursor();
$this->resetSignals();

throw $e;
}

if ($this->hint !== '') {


usleep(250_000);
}

$this->finish();

return $result;
}




public function start(): void
{
$this->capturePreviousNewLines();

if (function_exists('pcntl_signal')) {
$this->originalAsync = pcntl_async_signals(true);
pcntl_signal(SIGINT, function () {
$this->state = 'cancel';
$this->render();
exit();
});
}

$this->state = 'active';
$this->hideCursor();
$this->render();
}




public function advance(int $step = 1): void
{
$this->progress += $step;

if ($this->progress > $this->total) {
$this->progress = $this->total;
}

$this->render();
}




public function finish(): void
{
$this->state = 'submit';
$this->render();
$this->restoreCursor();
$this->resetSignals();
}




public function render(): void
{
parent::render();
}




public function label(string $label): static
{
$this->label = $label;

return $this;
}




public function hint(string $hint): static
{
$this->hint = $hint;

return $this;
}




public function percentage(): int|float
{
return $this->progress / $this->total;
}






public function prompt(): never
{
throw new RuntimeException('Progress Bar cannot be prompted.');
}




public function value(): bool
{
return true;
}




protected function resetSignals(): void
{
if (isset($this->originalAsync)) {
pcntl_async_signals($this->originalAsync);
pcntl_signal(SIGINT, SIG_DFL);
}
}




public function __destruct()
{
$this->restoreCursor();
}
}
