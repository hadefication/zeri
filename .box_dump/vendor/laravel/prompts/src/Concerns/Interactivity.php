<?php

namespace Laravel\Prompts\Concerns;

use Laravel\Prompts\Exceptions\NonInteractiveValidationException;

trait Interactivity
{



protected static bool $interactive;




public static function interactive(bool $interactive = true): void
{
static::$interactive = $interactive;
}




protected function default(): mixed
{
$default = $this->value();

$this->validate($default);

if ($this->state === 'error') {
throw new NonInteractiveValidationException($this->error);
}

return $default;
}
}
