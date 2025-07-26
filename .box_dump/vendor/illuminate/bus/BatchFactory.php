<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Factory as QueueFactory;

class BatchFactory
{





protected $queue;






public function __construct(QueueFactory $queue)
{
$this->queue = $queue;
}

















public function make(BatchRepository $repository,
string $id,
string $name,
int $totalJobs,
int $pendingJobs,
int $failedJobs,
array $failedJobIds,
array $options,
CarbonImmutable $createdAt,
?CarbonImmutable $cancelledAt,
?CarbonImmutable $finishedAt)
{
return new Batch($this->queue, $repository, $id, $name, $totalJobs, $pendingJobs, $failedJobs, $failedJobIds, $options, $createdAt, $cancelledAt, $finishedAt);
}
}
