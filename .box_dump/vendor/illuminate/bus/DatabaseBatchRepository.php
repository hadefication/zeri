<?php

namespace Illuminate\Bus;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Throwable;

class DatabaseBatchRepository implements PrunableBatchRepository
{





protected $factory;






protected $connection;






protected $table;








public function __construct(BatchFactory $factory, Connection $connection, string $table)
{
$this->factory = $factory;
$this->connection = $connection;
$this->table = $table;
}








public function get($limit = 50, $before = null)
{
return $this->connection->table($this->table)
->orderByDesc('id')
->limit($limit)
->when($before, fn ($q) => $q->where('id', '<', $before))
->get()
->map(function ($batch) {
return $this->toBatch($batch);
})
->all();
}







public function find(string $batchId)
{
$batch = $this->connection->table($this->table)
->useWritePdo()
->where('id', $batchId)
->first();

if ($batch) {
return $this->toBatch($batch);
}
}







public function store(PendingBatch $batch)
{
$id = (string) Str::orderedUuid();

$this->connection->table($this->table)->insert([
'id' => $id,
'name' => $batch->name,
'total_jobs' => 0,
'pending_jobs' => 0,
'failed_jobs' => 0,
'failed_job_ids' => '[]',
'options' => $this->serialize($batch->options),
'created_at' => time(),
'cancelled_at' => null,
'finished_at' => null,
]);

return $this->find($id);
}








public function incrementTotalJobs(string $batchId, int $amount)
{
$this->connection->table($this->table)->where('id', $batchId)->update([
'total_jobs' => new Expression('total_jobs + '.$amount),
'pending_jobs' => new Expression('pending_jobs + '.$amount),
'finished_at' => null,
]);
}








public function decrementPendingJobs(string $batchId, string $jobId)
{
$values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
return [
'pending_jobs' => $batch->pending_jobs - 1,
'failed_jobs' => $batch->failed_jobs,
'failed_job_ids' => json_encode(array_values(array_diff((array) json_decode($batch->failed_job_ids, true), [$jobId]))),
];
});

return new UpdatedBatchJobCounts(
$values['pending_jobs'],
$values['failed_jobs']
);
}








public function incrementFailedJobs(string $batchId, string $jobId)
{
$values = $this->updateAtomicValues($batchId, function ($batch) use ($jobId) {
return [
'pending_jobs' => $batch->pending_jobs,
'failed_jobs' => $batch->failed_jobs + 1,
'failed_job_ids' => json_encode(array_values(array_unique(array_merge((array) json_decode($batch->failed_job_ids, true), [$jobId])))),
];
});

return new UpdatedBatchJobCounts(
$values['pending_jobs'],
$values['failed_jobs']
);
}








protected function updateAtomicValues(string $batchId, Closure $callback)
{
return $this->connection->transaction(function () use ($batchId, $callback) {
$batch = $this->connection->table($this->table)->where('id', $batchId)
->lockForUpdate()
->first();

return is_null($batch) ? [] : tap($callback($batch), function ($values) use ($batchId) {
$this->connection->table($this->table)->where('id', $batchId)->update($values);
});
});
}







public function markAsFinished(string $batchId)
{
$this->connection->table($this->table)->where('id', $batchId)->update([
'finished_at' => time(),
]);
}







public function cancel(string $batchId)
{
$this->connection->table($this->table)->where('id', $batchId)->update([
'cancelled_at' => time(),
'finished_at' => time(),
]);
}







public function delete(string $batchId)
{
$this->connection->table($this->table)->where('id', $batchId)->delete();
}







public function prune(DateTimeInterface $before)
{
$query = $this->connection->table($this->table)
->whereNotNull('finished_at')
->where('finished_at', '<', $before->getTimestamp());

$totalDeleted = 0;

do {
$deleted = $query->limit(1000)->delete();

$totalDeleted += $deleted;
} while ($deleted !== 0);

return $totalDeleted;
}







public function pruneUnfinished(DateTimeInterface $before)
{
$query = $this->connection->table($this->table)
->whereNull('finished_at')
->where('created_at', '<', $before->getTimestamp());

$totalDeleted = 0;

do {
$deleted = $query->limit(1000)->delete();

$totalDeleted += $deleted;
} while ($deleted !== 0);

return $totalDeleted;
}







public function pruneCancelled(DateTimeInterface $before)
{
$query = $this->connection->table($this->table)
->whereNotNull('cancelled_at')
->where('created_at', '<', $before->getTimestamp());

$totalDeleted = 0;

do {
$deleted = $query->limit(1000)->delete();

$totalDeleted += $deleted;
} while ($deleted !== 0);

return $totalDeleted;
}







public function transaction(Closure $callback)
{
return $this->connection->transaction(fn () => $callback());
}






public function rollBack()
{
$this->connection->rollBack(toLevel: 0);
}







protected function serialize($value)
{
$serialized = serialize($value);

return $this->connection instanceof PostgresConnection
? base64_encode($serialized)
: $serialized;
}







protected function unserialize($serialized)
{
if ($this->connection instanceof PostgresConnection &&
! Str::contains($serialized, [':', ';'])) {
$serialized = base64_decode($serialized);
}

try {
return unserialize($serialized);
} catch (Throwable) {
return [];
}
}







protected function toBatch($batch)
{
return $this->factory->make(
$this,
$batch->id,
$batch->name,
(int) $batch->total_jobs,
(int) $batch->pending_jobs,
(int) $batch->failed_jobs,
(array) json_decode($batch->failed_job_ids, true),
$this->unserialize($batch->options),
CarbonImmutable::createFromTimestamp($batch->created_at, date_default_timezone_get()),
$batch->cancelled_at ? CarbonImmutable::createFromTimestamp($batch->cancelled_at, date_default_timezone_get()) : $batch->cancelled_at,
$batch->finished_at ? CarbonImmutable::createFromTimestamp($batch->finished_at, date_default_timezone_get()) : $batch->finished_at
);
}






public function getConnection()
{
return $this->connection;
}







public function setConnection(Connection $connection)
{
$this->connection = $connection;
}
}
