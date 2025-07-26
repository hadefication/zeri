<?php

namespace Illuminate\Foundation\Testing;

use Illuminate\Database\DatabaseTransactionsManager as BaseManager;

class DatabaseTransactionsManager extends BaseManager
{



protected array $connectionsTransacting;






public function __construct(array $connectionsTransacting)
{
parent::__construct();

$this->connectionsTransacting = $connectionsTransacting;
}







public function addCallback($callback)
{



if ($this->callbackApplicableTransactions()->count() === 0) {
return $callback();
}

$this->pendingTransactions->last()->addCallback($callback);
}






public function callbackApplicableTransactions()
{
return $this->pendingTransactions->skip(count($this->connectionsTransacting))->values();
}







public function afterCommitCallbacksShouldBeExecuted($level)
{
return $level === 1;
}
}
