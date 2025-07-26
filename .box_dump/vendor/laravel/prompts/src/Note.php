<?php

namespace Laravel\Prompts;

class Note extends Prompt
{



public function __construct(public string $message, public ?string $type = null)
{

}




public function display(): void
{
$this->prompt();
}




public function prompt(): bool
{
$this->capturePreviousNewLines();

$this->state = 'submit';

static::output()->write($this->renderTheme());

return true;
}




public function value(): bool
{
return true;
}
}
