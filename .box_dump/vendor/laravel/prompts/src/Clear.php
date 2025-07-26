<?php

namespace Laravel\Prompts;

class Clear extends Prompt
{



public function prompt(): bool
{

static::output()->write(PHP_EOL.PHP_EOL);

$this->writeDirectly($this->renderTheme());

return true;
}




public function display(): void
{
$this->prompt();
}




public function value(): bool
{
return true;
}
}
