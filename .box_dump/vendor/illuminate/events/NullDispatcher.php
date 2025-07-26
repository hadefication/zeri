<?php

namespace Illuminate\Events;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\Traits\ForwardsCalls;

class NullDispatcher implements DispatcherContract
{
use ForwardsCalls;






protected $dispatcher;






public function __construct(DispatcherContract $dispatcher)
{
$this->dispatcher = $dispatcher;
}









public function dispatch($event, $payload = [], $halt = false)
{

}








public function push($event, $payload = [])
{

}








public function until($event, $payload = [])
{

}








public function listen($events, $listener = null)
{
$this->dispatcher->listen($events, $listener);
}







public function hasListeners($eventName)
{
return $this->dispatcher->hasListeners($eventName);
}







public function subscribe($subscriber)
{
$this->dispatcher->subscribe($subscriber);
}







public function flush($event)
{
$this->dispatcher->flush($event);
}







public function forget($event)
{
$this->dispatcher->forget($event);
}






public function forgetPushed()
{
$this->dispatcher->forgetPushed();
}








public function __call($method, $parameters)
{
return $this->forwardDecoratedCallTo($this->dispatcher, $method, $parameters);
}
}
