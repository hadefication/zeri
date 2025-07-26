<?php

namespace Illuminate\Events;

use Illuminate\Support\Collection;

class InvokeQueuedClosure
{







public function handle($closure, array $arguments)
{
call_user_func($closure->getClosure(), ...$arguments);
}










public function failed($closure, array $arguments, array $catchCallbacks, $exception)
{
$arguments[] = $exception;

(new Collection($catchCallbacks))->each->__invoke(...$arguments);
}
}
