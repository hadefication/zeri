<?php

namespace Illuminate\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallQueuedListener implements ShouldQueue
{
use InteractsWithQueue, Queueable;






public $class;






public $method;






public $data;






public $tries;






public $maxExceptions;






public $backoff;






public $retryUntil;






public $timeout;






public $failOnTimeout = false;






public $shouldBeEncrypted = false;








public function __construct($class, $method, $data)
{
$this->data = $data;
$this->class = $class;
$this->method = $method;
}







public function handle(Container $container)
{
$this->prepareData();

$handler = $this->setJobInstanceIfNecessary(
$this->job, $container->make($this->class)
);

$handler->{$this->method}(...array_values($this->data));
}








protected function setJobInstanceIfNecessary(Job $job, $instance)
{
if (in_array(InteractsWithQueue::class, class_uses_recursive($instance))) {
$instance->setJob($job);
}

return $instance;
}









public function failed($e)
{
$this->prepareData();

$handler = Container::getInstance()->make($this->class);

$parameters = array_merge(array_values($this->data), [$e]);

if (method_exists($handler, 'failed')) {
$handler->failed(...$parameters);
}
}






protected function prepareData()
{
if (is_string($this->data)) {
$this->data = unserialize($this->data);
}
}






public function displayName()
{
return $this->class;
}






public function __clone()
{
$this->data = array_map(function ($data) {
return is_object($data) ? clone $data : $data;
}, $this->data);
}
}
