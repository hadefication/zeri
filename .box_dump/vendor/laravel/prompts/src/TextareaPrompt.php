<?php

namespace Laravel\Prompts;

use Closure;
use Laravel\Prompts\Support\Utils;

class TextareaPrompt extends Prompt
{
use Concerns\Scrolling;
use Concerns\Truncation;
use Concerns\TypedValue;




public int $width = 60;




public function __construct(
public string $label,
public string $placeholder = '',
public string $default = '',
public bool|string $required = false,
public mixed $validate = null,
public string $hint = '',
int $rows = 5,
public ?Closure $transform = null,
) {
$this->scroll = $rows;

$this->initializeScrolling();

$this->trackTypedValue(
default: $default,
submit: false,
allowNewLine: true,
);

$this->on('key', function ($key) {
if ($key[0] === "\e") {
match ($key) {
Key::UP, Key::UP_ARROW, Key::CTRL_P => $this->handleUpKey(),
Key::DOWN, Key::DOWN_ARROW, Key::CTRL_N => $this->handleDownKey(),
default => null,
};

return;
}


foreach (mb_str_split($key) as $key) {
if ($key === Key::CTRL_D) {
$this->submit();

return;
}
}
});
}




public function valueWithCursor(): string
{
if ($this->value() === '') {
return $this->wrappedPlaceholderWithCursor();
}

return $this->addCursor($this->wrappedValue(), $this->cursorPosition + $this->cursorOffset(), -1);
}




public function wrappedValue(): string
{
return $this->mbWordwrap($this->value(), $this->width, PHP_EOL, true);
}






public function lines(): array
{
return explode(PHP_EOL, $this->wrappedValue());
}






public function visible(): array
{
$this->adjustVisibleWindow();

$withCursor = $this->valueWithCursor();

return array_slice(explode(PHP_EOL, $withCursor), $this->firstVisible, $this->scroll, preserve_keys: true);
}




protected function handleUpKey(): void
{
if ($this->cursorPosition === 0) {
return;
}

$lines = $this->lines();


$lineLengths = array_map(fn ($line, $index) => mb_strlen($line) + ($index === count($lines) - 1 ? 0 : 1), $lines, range(0, count($lines) - 1));

$currentLineIndex = $this->currentLineIndex();

if ($currentLineIndex === 0) {

$this->cursorPosition = 0;

return;
}

$currentLines = array_slice($lineLengths, 0, $currentLineIndex + 1);

$currentColumn = Utils::last($currentLines) - (array_sum($currentLines) - $this->cursorPosition);

$destinationLineLength = ($lineLengths[$currentLineIndex - 1] ?? $currentLines[0]) - 1;

$newColumn = min($destinationLineLength, $currentColumn);

$fullLines = array_slice($currentLines, 0, -2);

$this->cursorPosition = array_sum($fullLines) + $newColumn;
}




protected function handleDownKey(): void
{
$lines = $this->lines();


$lineLengths = array_map(fn ($line, $index) => mb_strlen($line) + ($index === count($lines) - 1 ? 0 : 1), $lines, range(0, count($lines) - 1));

$currentLineIndex = $this->currentLineIndex();

if ($currentLineIndex === count($lines) - 1) {

$this->cursorPosition = mb_strlen(implode(PHP_EOL, $lines));

return;
}


$currentLines = array_slice($lineLengths, 0, $currentLineIndex + 1);

$currentColumn = Utils::last($currentLines) - (array_sum($currentLines) - $this->cursorPosition);

$destinationLineLength = $lineLengths[$currentLineIndex + 1] ?? Utils::last($currentLines);

if ($currentLineIndex + 1 !== count($lines) - 1) {
$destinationLineLength--;
}

$newColumn = min(max(0, $destinationLineLength), $currentColumn);

$this->cursorPosition = array_sum($currentLines) + $newColumn;
}




protected function adjustVisibleWindow(): void
{
if (count($this->lines()) < $this->scroll) {
return;
}

$currentLineIndex = $this->currentLineIndex();

while ($this->firstVisible + $this->scroll <= $currentLineIndex) {
$this->firstVisible++;
}

if ($currentLineIndex === $this->firstVisible - 1) {
$this->firstVisible = max(0, $this->firstVisible - 1);
}


if ($this->firstVisible + $this->scroll > count($this->lines())) {
$this->firstVisible = count($this->lines()) - $this->scroll;
}
}




protected function currentLineIndex(): int
{
$totalLineLength = 0;

return (int) Utils::search($this->lines(), function ($line) use (&$totalLineLength) {
$totalLineLength += mb_strlen($line) + 1;

return $totalLineLength > $this->cursorPosition;
}) ?: 0;
}




protected function cursorOffset(): int
{
$cursorOffset = 0;

preg_match_all('/\S{'.$this->width.',}/u', $this->value(), $matches, PREG_OFFSET_CAPTURE);

foreach ($matches[0] as $match) {
if ($this->cursorPosition + $cursorOffset >= $match[1] + mb_strwidth($match[0])) {
$cursorOffset += (int) floor(mb_strwidth($match[0]) / $this->width);
}
}

return $cursorOffset;
}




protected function wrappedPlaceholderWithCursor(): string
{
return implode(PHP_EOL, array_map(
$this->dim(...),
explode(PHP_EOL, $this->addCursor(
$this->mbWordwrap($this->placeholder, $this->width, PHP_EOL, true),
cursorPosition: 0,
))
));
}
}
