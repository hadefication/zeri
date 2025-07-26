<?php

namespace Laravel\Prompts;

use Illuminate\Support\Collection;

class Table extends Prompt
{





public array $headers;






public array $rows;

/**
@phpstan-param($rows is null ? list<list<string>>|Collection<int, list<string>> : list<string|list<string>>|Collection<int, string|list<string>>) $headers





*/
public function __construct(array|Collection $headers = [], array|Collection|null $rows = null)
{
if ($rows === null) {
$rows = $headers;
$headers = [];
}

$this->headers = $headers instanceof Collection ? $headers->all() : $headers;
$this->rows = $rows instanceof Collection ? $rows->all() : $rows;
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
