<?php

namespace Illuminate\Foundation\Bus;

use Illuminate\Bus\UniqueLock;
use Illuminate\Container\Container;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\InteractsWithUniqueJobs;

class PendingDispatch
{
use InteractsWithUniqueJobs;






protected $job;






protected $afterResponse = false;






public function __construct($job)
{
$this->job = $job;
}







public function onConnection($connection)
{
$this->job->onConnection($connection);

return $this;
}







public function onQueue($queue)
{
$this->job->onQueue($queue);

return $this;
}







public function allOnConnection($connection)
{
$this->job->allOnConnection($connection);

return $this;
}







public function allOnQueue($queue)
{
$this->job->allOnQueue($queue);

return $this;
}







public function delay($delay)
{
$this->job->delay($delay);

return $this;
}






public function withoutDelay()
{
$this->job->withoutDelay();

return $this;
}






public function afterCommit()
{
$this->job->afterCommit();

return $this;
}






public function beforeCommit()
{
$this->job->beforeCommit();

return $this;
}







public function chain($chain)
{
$this->job->chain($chain);

return $this;
}






public function afterResponse()
{
$this->afterResponse = true;

return $this;
}






protected function shouldDispatch()
{
if (! $this->job instanceof ShouldBeUnique) {
return true;
}

return (new UniqueLock(Container::getInstance()->make(Cache::class)))
->acquire($this->job);
}






public function getJob()
{
return $this->job;
}








public function __call($method, $parameters)
{
$this->job->{$method}(...$parameters);

return $this;
}






public function __destruct()
{
$this->addUniqueJobInformationToContext($this->job);

if (! $this->shouldDispatch()) {
$this->removeUniqueJobInformationFromContext($this->job);

return;
} elseif ($this->afterResponse) {
app(Dispatcher::class)->dispatchAfterResponse($this->job);
} else {
app(Dispatcher::class)->dispatch($this->job);
}

$this->removeUniqueJobInformationFromContext($this->job);
}
}
