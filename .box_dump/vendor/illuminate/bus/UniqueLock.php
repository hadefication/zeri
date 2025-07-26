<?php

namespace Illuminate\Bus;

use Illuminate\Contracts\Cache\Repository as Cache;

class UniqueLock
{





protected $cache;






public function __construct(Cache $cache)
{
$this->cache = $cache;
}







public function acquire($job)
{
$uniqueFor = method_exists($job, 'uniqueFor')
? $job->uniqueFor()
: ($job->uniqueFor ?? 0);

$cache = method_exists($job, 'uniqueVia')
? $job->uniqueVia()
: $this->cache;

return (bool) $cache->lock($this->getKey($job), $uniqueFor)->get();
}







public function release($job)
{
$cache = method_exists($job, 'uniqueVia')
? $job->uniqueVia()
: $this->cache;

$cache->lock($this->getKey($job))->forceRelease();
}







public static function getKey($job)
{
$uniqueId = method_exists($job, 'uniqueId')
? $job->uniqueId()
: ($job->uniqueId ?? '');

return 'laravel_unique_job:'.get_class($job).':'.$uniqueId;
}
}
