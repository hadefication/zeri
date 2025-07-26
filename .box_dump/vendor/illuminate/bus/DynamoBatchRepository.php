<?php

namespace Illuminate\Bus;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Support\Str;

class DynamoBatchRepository implements BatchRepository
{





protected $factory;






protected $dynamoDbClient;






protected $applicationName;






protected $table;






protected $ttl;






protected $ttlAttribute;






protected $marshaler;




public function __construct(
BatchFactory $factory,
DynamoDbClient $dynamoDbClient,
string $applicationName,
string $table,
?int $ttl,
?string $ttlAttribute,
) {
$this->factory = $factory;
$this->dynamoDbClient = $dynamoDbClient;
$this->applicationName = $applicationName;
$this->table = $table;
$this->ttl = $ttl;
$this->ttlAttribute = $ttlAttribute;
$this->marshaler = new Marshaler;
}








public function get($limit = 50, $before = null)
{
$condition = 'application = :application';

if ($before) {
$condition = 'application = :application AND id < :id';
}

$result = $this->dynamoDbClient->query([
'TableName' => $this->table,
'KeyConditionExpression' => $condition,
'ExpressionAttributeValues' => array_filter([
':application' => ['S' => $this->applicationName],
':id' => array_filter(['S' => $before]),
]),
'Limit' => $limit,
'ScanIndexForward' => false,
]);

return array_map(
fn ($b) => $this->toBatch($this->marshaler->unmarshalItem($b, mapAsObject: true)),
$result['Items']
);
}







public function find(string $batchId)
{
if ($batchId === '') {
return null;
}

$b = $this->dynamoDbClient->getItem([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
]);

if (! isset($b['Item'])) {

$b = $this->dynamoDbClient->getItem([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'ConsistentRead' => true,
]);

if (! isset($b['Item'])) {
return null;
}
}

$batch = $this->marshaler->unmarshalItem($b['Item'], mapAsObject: true);

if ($batch) {
return $this->toBatch($batch);
}
}







public function store(PendingBatch $batch)
{
$id = (string) Str::orderedUuid();

$batch = [
'id' => $id,
'name' => $batch->name,
'total_jobs' => 0,
'pending_jobs' => 0,
'failed_jobs' => 0,
'failed_job_ids' => [],
'options' => $this->serialize($batch->options ?? []),
'created_at' => time(),
'cancelled_at' => null,
'finished_at' => null,
];

if (! is_null($this->ttl)) {
$batch[$this->ttlAttribute] = time() + $this->ttl;
}

$this->dynamoDbClient->putItem([
'TableName' => $this->table,
'Item' => $this->marshaler->marshalItem(
array_merge(['application' => $this->applicationName], $batch)
),
]);

return $this->find($id);
}








public function incrementTotalJobs(string $batchId, int $amount)
{
$update = 'SET total_jobs = total_jobs + :val, pending_jobs = pending_jobs + :val';

if ($this->ttl) {
$update = "SET total_jobs = total_jobs + :val, pending_jobs = pending_jobs + :val, #{$this->ttlAttribute} = :ttl";
}

$this->dynamoDbClient->updateItem(array_filter([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'UpdateExpression' => $update,
'ExpressionAttributeValues' => array_filter([
':val' => ['N' => "$amount"],
':ttl' => array_filter(['N' => $this->getExpiryTime()]),
]),
'ExpressionAttributeNames' => $this->ttlExpressionAttributeName(),
'ReturnValues' => 'ALL_NEW',
]));
}








public function decrementPendingJobs(string $batchId, string $jobId)
{
$update = 'SET pending_jobs = pending_jobs - :inc';

if ($this->ttl !== null) {
$update = "SET pending_jobs = pending_jobs - :inc, #{$this->ttlAttribute} = :ttl";
}

$batch = $this->dynamoDbClient->updateItem(array_filter([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'UpdateExpression' => $update,
'ExpressionAttributeValues' => array_filter([
':inc' => ['N' => '1'],
':ttl' => array_filter(['N' => $this->getExpiryTime()]),
]),
'ExpressionAttributeNames' => $this->ttlExpressionAttributeName(),
'ReturnValues' => 'ALL_NEW',
]));

$values = $this->marshaler->unmarshalItem($batch['Attributes']);

return new UpdatedBatchJobCounts(
$values['pending_jobs'],
$values['failed_jobs']
);
}








public function incrementFailedJobs(string $batchId, string $jobId)
{
$update = 'SET failed_jobs = failed_jobs + :inc, failed_job_ids = list_append(failed_job_ids, :jobId)';

if ($this->ttl !== null) {
$update = "SET failed_jobs = failed_jobs + :inc, failed_job_ids = list_append(failed_job_ids, :jobId), #{$this->ttlAttribute} = :ttl";
}

$batch = $this->dynamoDbClient->updateItem(array_filter([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'UpdateExpression' => $update,
'ExpressionAttributeValues' => array_filter([
':jobId' => $this->marshaler->marshalValue([$jobId]),
':inc' => ['N' => '1'],
':ttl' => array_filter(['N' => $this->getExpiryTime()]),
]),
'ExpressionAttributeNames' => $this->ttlExpressionAttributeName(),
'ReturnValues' => 'ALL_NEW',
]));

$values = $this->marshaler->unmarshalItem($batch['Attributes']);

return new UpdatedBatchJobCounts(
$values['pending_jobs'],
$values['failed_jobs']
);
}







public function markAsFinished(string $batchId)
{
$update = 'SET finished_at = :timestamp';

if ($this->ttl !== null) {
$update = "SET finished_at = :timestamp, #{$this->ttlAttribute} = :ttl";
}

$this->dynamoDbClient->updateItem(array_filter([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'UpdateExpression' => $update,
'ExpressionAttributeValues' => array_filter([
':timestamp' => ['N' => (string) time()],
':ttl' => array_filter(['N' => $this->getExpiryTime()]),
]),
'ExpressionAttributeNames' => $this->ttlExpressionAttributeName(),
]));
}







public function cancel(string $batchId)
{
$update = 'SET cancelled_at = :timestamp, finished_at = :timestamp';

if ($this->ttl !== null) {
$update = "SET cancelled_at = :timestamp, finished_at = :timestamp, #{$this->ttlAttribute} = :ttl";
}

$this->dynamoDbClient->updateItem(array_filter([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
'UpdateExpression' => $update,
'ExpressionAttributeValues' => array_filter([
':timestamp' => ['N' => (string) time()],
':ttl' => array_filter(['N' => $this->getExpiryTime()]),
]),
'ExpressionAttributeNames' => $this->ttlExpressionAttributeName(),
]));
}







public function delete(string $batchId)
{
$this->dynamoDbClient->deleteItem([
'TableName' => $this->table,
'Key' => [
'application' => ['S' => $this->applicationName],
'id' => ['S' => $batchId],
],
]);
}







public function transaction(Closure $callback)
{
return $callback();
}






public function rollBack()
{
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
$batch->failed_job_ids,
$this->unserialize($batch->options) ?? [],
CarbonImmutable::createFromTimestamp($batch->created_at, date_default_timezone_get()),
$batch->cancelled_at ? CarbonImmutable::createFromTimestamp($batch->cancelled_at, date_default_timezone_get()) : $batch->cancelled_at,
$batch->finished_at ? CarbonImmutable::createFromTimestamp($batch->finished_at, date_default_timezone_get()) : $batch->finished_at
);
}






public function createAwsDynamoTable(): void
{
$definition = [
'TableName' => $this->table,
'AttributeDefinitions' => [
[
'AttributeName' => 'application',
'AttributeType' => 'S',
],
[
'AttributeName' => 'id',
'AttributeType' => 'S',
],
],
'KeySchema' => [
[
'AttributeName' => 'application',
'KeyType' => 'HASH',
],
[
'AttributeName' => 'id',
'KeyType' => 'RANGE',
],
],
'BillingMode' => 'PAY_PER_REQUEST',
];

$this->dynamoDbClient->createTable($definition);

if (! is_null($this->ttl)) {
$this->dynamoDbClient->updateTimeToLive([
'TableName' => $this->table,
'TimeToLiveSpecification' => [
'AttributeName' => $this->ttlAttribute,
'Enabled' => true,
],
]);
}
}




public function deleteAwsDynamoTable(): void
{
$this->dynamoDbClient->deleteTable([
'TableName' => $this->table,
]);
}






protected function getExpiryTime(): ?string
{
return is_null($this->ttl) ? null : (string) (time() + $this->ttl);
}






protected function ttlExpressionAttributeName(): array
{
return is_null($this->ttl) ? [] : ["#{$this->ttlAttribute}" => $this->ttlAttribute];
}







protected function serialize($value)
{
return serialize($value);
}







protected function unserialize($serialized)
{
return unserialize($serialized);
}






public function getDynamoClient(): DynamoDbClient
{
return $this->dynamoDbClient;
}






public function getTable(): string
{
return $this->table;
}
}
