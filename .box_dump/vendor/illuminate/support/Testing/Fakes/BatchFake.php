<?php

namespace Illuminate\Support\Testing\Fakes;

use Carbon\CarbonImmutable;
use Illuminate\Bus\Batch;
use Illuminate\Bus\UpdatedBatchJobCounts;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BatchFake extends Batch
{





public $added = [];






public $deleted = false;















public function __construct(
string $id,
string $name,
int $totalJobs,
int $pendingJobs,
int $failedJobs,
array $failedJobIds,
array $options,
CarbonImmutable $createdAt,
?CarbonImmutable $cancelledAt = null,
?CarbonImmutable $finishedAt = null,
) {
$this->id = $id;
$this->name = $name;
$this->totalJobs = $totalJobs;
$this->pendingJobs = $pendingJobs;
$this->failedJobs = $failedJobs;
$this->failedJobIds = $failedJobIds;
$this->options = $options;
$this->createdAt = $createdAt;
$this->cancelledAt = $cancelledAt;
$this->finishedAt = $finishedAt;
}






public function fresh()
{
return $this;
}







public function add($jobs)
{
$jobs = Collection::wrap($jobs);

foreach ($jobs as $job) {
$this->added[] = $job;
}

$this->totalJobs += $jobs->count();

return $this;
}







public function recordSuccessfulJob(string $jobId)
{

}







public function decrementPendingJobs(string $jobId)
{

}








public function recordFailedJob(string $jobId, $e)
{

}







public function incrementFailedJobs(string $jobId)
{
return new UpdatedBatchJobCounts;
}






public function cancel()
{
$this->cancelledAt = Carbon::now();
}






public function delete()
{
$this->deleted = true;
}






public function deleted()
{
return $this->deleted;
}
}
