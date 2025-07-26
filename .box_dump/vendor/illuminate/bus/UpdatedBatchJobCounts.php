<?php

namespace Illuminate\Bus;

class UpdatedBatchJobCounts
{





public $pendingJobs;






public $failedJobs;







public function __construct(int $pendingJobs = 0, int $failedJobs = 0)
{
$this->pendingJobs = $pendingJobs;
$this->failedJobs = $failedJobs;
}






public function allJobsHaveRanExactlyOnce()
{
return ($this->pendingJobs - $this->failedJobs) === 0;
}
}
