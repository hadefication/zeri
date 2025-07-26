<?php

namespace Illuminate\Foundation\Exceptions\Renderer;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;

class Listener
{





protected $queries = [];







public function registerListeners(Dispatcher $events)
{
$events->listen(QueryExecuted::class, $this->onQueryExecuted(...));

$events->listen([JobProcessing::class, JobProcessed::class], function () {
$this->queries = [];
});

if (isset($_SERVER['LARAVEL_OCTANE'])) {
$events->listen([RequestReceived::class, TaskReceived::class, TickReceived::class, RequestTerminated::class], function () {
$this->queries = [];
});
}
}






public function queries()
{
return $this->queries;
}







public function onQueryExecuted(QueryExecuted $event)
{
if (count($this->queries) === 100) {
return;
}

$this->queries[] = [
'connectionName' => $event->connectionName,
'time' => $event->time,
'sql' => $event->sql,
'bindings' => $event->bindings,
];
}
}
