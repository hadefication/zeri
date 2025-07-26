<?php

namespace Illuminate\Foundation\Queue;

use Illuminate\Bus\UniqueLock;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Context;

trait InteractsWithUniqueJobs
{






public function addUniqueJobInformationToContext($job): void
{
if ($job instanceof ShouldBeUnique) {
Context::addHidden([
'laravel_unique_job_cache_store' => $this->getUniqueJobCacheStore($job),
'laravel_unique_job_key' => UniqueLock::getKey($job),
]);
}
}







public function removeUniqueJobInformationFromContext($job): void
{
if ($job instanceof ShouldBeUnique) {
Context::forgetHidden([
'laravel_unique_job_cache_store',
'laravel_unique_job_key',
]);
}
}







protected function getUniqueJobCacheStore($job): ?string
{
return method_exists($job, 'uniqueVia')
? $job->uniqueVia()->getName()
: config('cache.default');
}
}
