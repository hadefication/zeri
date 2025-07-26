<?php

namespace Illuminate\Support;

use Closure;

class Benchmark
{







public static function measure(Closure|array $benchmarkables, int $iterations = 1): array|float
{
return Collection::wrap($benchmarkables)->map(function ($callback) use ($iterations) {
return Collection::range(1, $iterations)->map(function () use ($callback) {
gc_collect_cycles();

$start = hrtime(true);

$callback();

return (hrtime(true) - $start) / 1_000_000;
})->average();
})->when(
$benchmarkables instanceof Closure,
fn ($c) => $c->first(),
fn ($c) => $c->all(),
);
}

/**
@template





*/
public static function value(callable $callback): array
{
gc_collect_cycles();

$start = hrtime(true);

$result = $callback();

return [$result, (hrtime(true) - $start) / 1_000_000];
}








public static function dd(Closure|array $benchmarkables, int $iterations = 1): void
{
$result = (new Collection(static::measure(Arr::wrap($benchmarkables), $iterations)))
->map(fn ($average) => number_format($average, 3).'ms')
->when($benchmarkables instanceof Closure, fn ($c) => $c->first(), fn ($c) => $c->all());

dd($result);
}
}
