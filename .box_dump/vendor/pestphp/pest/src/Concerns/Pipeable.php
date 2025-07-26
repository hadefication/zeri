<?php

declare(strict_types=1);

namespace Pest\Concerns;

use Closure;




trait Pipeable
{





private static array $pipes = [];






private static array $interceptors = [];




public function pipe(string $name, Closure $pipe): void
{
self::$pipes[$name][] = $pipe;
}






public function intercept(string $name, string|Closure $filter, Closure $handler): void
{
if (is_string($filter)) {
$filter = fn ($value): bool => $value instanceof $filter;
}

self::$interceptors[$name][] = $handler;

$this->pipe($name, function ($next, ...$arguments) use ($handler, $filter): void {

if ($filter($this->value, ...$arguments)) {

$handler->bindTo($this, $this::class)(...$arguments);

return;
}

$next();
});
}






private function pipes(string $name, object $context, string $scope): array
{
return array_map(fn (Closure $pipe): \Closure => $pipe->bindTo($context, $scope), self::$pipes[$name] ?? []);
}
}
