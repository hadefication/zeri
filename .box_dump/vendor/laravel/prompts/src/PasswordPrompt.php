<?php

namespace Laravel\Prompts;

use Closure;

class PasswordPrompt extends Prompt
{
use Concerns\TypedValue;




public function __construct(
public string $label,
public string $placeholder = '',
public bool|string $required = false,
public mixed $validate = null,
public string $hint = '',
public ?Closure $transform = null,
) {
$this->trackTypedValue();
}




public function masked(): string
{
return str_repeat('•', mb_strlen($this->value()));
}




public function maskedWithCursor(int $maxWidth): string
{
if ($this->value() === '') {
return $this->dim($this->addCursor($this->placeholder, 0, $maxWidth));
}

return $this->addCursor($this->masked(), $this->cursorPosition, $maxWidth);
}
}
