<?php

namespace Illuminate\Contracts\Redis;

use Closure;

interface Connection
{







public function subscribe($channels, Closure $callback);








public function psubscribe($channels, Closure $callback);








public function command($method, array $parameters = []);
}
