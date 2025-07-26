<?php

namespace Laravel\Prompts\Concerns;

use Closure;

trait Events
{





protected array $listeners = [];




public function on(string $event, Closure $callback): void
{
$this->listeners[$event][] = $callback;
}




public function emit(string $event, mixed ...$data): void
{
foreach ($this->listeners[$event] ?? [] as $listener) {
$listener(...$data);
}
}




public function clearListeners(): void
{
$this->listeners = [];
}
}
