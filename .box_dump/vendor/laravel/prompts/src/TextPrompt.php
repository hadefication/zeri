<?php

namespace Laravel\Prompts;

use Closure;

class TextPrompt extends Prompt
{
use Concerns\TypedValue;




public function __construct(
public string $label,
public string $placeholder = '',
public string $default = '',
public bool|string $required = false,
public mixed $validate = null,
public string $hint = '',
public ?Closure $transform = null,
) {
$this->trackTypedValue($default);
}




public function valueWithCursor(int $maxWidth): string
{
if ($this->value() === '') {
return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
}

return $this->addCursor($this->value(), $this->cursorPosition, $maxWidth);
}
}
