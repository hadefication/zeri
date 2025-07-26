<?php

declare(strict_types=1);

namespace Termwind;

use Symfony\Component\Console\Terminal as ConsoleTerminal;




final class Terminal
{



private ConsoleTerminal $terminal;




public function __construct(?ConsoleTerminal $terminal = null)
{
$this->terminal = $terminal ?? new ConsoleTerminal;
}




public function width(): int
{
return $this->terminal->getWidth();
}




public function height(): int
{
return $this->terminal->getHeight();
}




public function clear(): void
{
Termwind::getRenderer()->write("\ec");
}
}
