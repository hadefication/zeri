<?php

namespace Illuminate\Support\Defer;

use Illuminate\Support\Str;

class DeferredCallback
{





public function __construct(public $callback, public ?string $name = null, public bool $always = false)
{
$this->name = $name ?? (string) Str::uuid();
}







public function name(string $name): static
{
$this->name = $name;

return $this;
}







public function always(bool $always = true): static
{
$this->always = $always;

return $this;
}






public function __invoke(): void
{
call_user_func($this->callback);
}
}
