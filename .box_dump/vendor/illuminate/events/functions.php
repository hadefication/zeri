<?php

namespace Illuminate\Events;

use Closure;

if (! function_exists('Illuminate\Events\queueable')) {






function queueable(Closure $closure)
{
return new QueuedClosure($closure);
}
}
