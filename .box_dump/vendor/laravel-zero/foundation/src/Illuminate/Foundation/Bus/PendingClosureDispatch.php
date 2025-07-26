<?php

namespace Illuminate\Foundation\Bus;

use Closure;

class PendingClosureDispatch extends PendingDispatch
{






public function catch(Closure $callback)
{
$this->job->onFailure($callback);

return $this;
}
}
