<?php

namespace Illuminate\Contracts\Queue;

interface Job
{





public function uuid();






public function getJobId();






public function payload();






public function fire();







public function release($delay = 0);






public function isReleased();






public function delete();






public function isDeleted();






public function isDeletedOrReleased();






public function attempts();






public function hasFailed();






public function markAsFailed();







public function fail($e = null);






public function maxTries();






public function maxExceptions();






public function timeout();






public function retryUntil();






public function getName();








public function resolveName();








public function resolveQueuedJobClass();






public function getConnectionName();






public function getQueue();






public function getRawBody();
}
