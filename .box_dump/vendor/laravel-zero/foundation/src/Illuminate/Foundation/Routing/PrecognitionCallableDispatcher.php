<?php

namespace Illuminate\Foundation\Routing;

use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\Route;

class PrecognitionCallableDispatcher extends CallableDispatcher
{







public function dispatch(Route $route, $callable)
{
$this->resolveParameters($route, $callable);

abort(204, headers: ['Precognition-Success' => 'true']);
}
}
