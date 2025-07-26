<?php

namespace Laravel\Prompts\Concerns;

trait Cursor
{



protected static bool $cursorHidden = false;




public function hideCursor(): void
{
static::writeDirectly("\e[?25l");

static::$cursorHidden = true;
}




public function showCursor(): void
{
static::writeDirectly("\e[?25h");

static::$cursorHidden = false;
}




public function restoreCursor(): void
{
if (static::$cursorHidden) {
$this->showCursor();
}
}




public function moveCursor(int $x, int $y = 0): void
{
$sequence = '';

if ($x < 0) {
$sequence .= "\e[".abs($x).'D'; 
} elseif ($x > 0) {
$sequence .= "\e[{$x}C"; 
}

if ($y < 0) {
$sequence .= "\e[".abs($y).'A'; 
} elseif ($y > 0) {
$sequence .= "\e[{$y}B"; 
}

static::writeDirectly($sequence);
}




public function moveCursorToColumn(int $column): void
{
static::writeDirectly("\e[{$column}G");
}




public function moveCursorUp(int $lines): void
{
static::writeDirectly("\e[{$lines}A");
}
}
