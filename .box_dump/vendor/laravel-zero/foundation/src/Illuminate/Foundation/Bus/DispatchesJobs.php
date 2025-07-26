<?php

namespace Illuminate\Foundation\Bus;

trait DispatchesJobs
{






protected function dispatch($job)
{
return dispatch($job);
}









public function dispatchSync($job)
{
return dispatch_sync($job);
}
}
