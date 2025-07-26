<?php

namespace Illuminate\Contracts\Concurrency;

use Closure;
use Illuminate\Support\Defer\DeferredCallback;

interface Driver
{



public function run(Closure|array $tasks): array;




public function defer(Closure|array $tasks): DeferredCallback;
}
