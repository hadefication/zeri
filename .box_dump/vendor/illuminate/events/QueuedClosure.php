<?php

namespace Illuminate\Events;

use Closure;
use Illuminate\Support\Collection;
use Laravel\SerializableClosure\SerializableClosure;

use function Illuminate\Support\enum_value;

class QueuedClosure
{





public $closure;






public $connection;






public $queue;






public $delay;






public $catchCallbacks = [];






public function __construct(Closure $closure)
{
$this->closure = $closure;
}







public function onConnection($connection)
{
$this->connection = enum_value($connection);

return $this;
}







public function onQueue($queue)
{
$this->queue = enum_value($queue);

return $this;
}







public function delay($delay)
{
$this->delay = $delay;

return $this;
}







public function catch(Closure $closure)
{
$this->catchCallbacks[] = $closure;

return $this;
}






public function resolve()
{
return function (...$arguments) {
dispatch(new CallQueuedListener(InvokeQueuedClosure::class, 'handle', [
'closure' => new SerializableClosure($this->closure),
'arguments' => $arguments,
'catch' => (new Collection($this->catchCallbacks))
->map(fn ($callback) => new SerializableClosure($callback))
->all(),
]))->onConnection($this->connection)->onQueue($this->queue)->delay($this->delay);
};
}
}
