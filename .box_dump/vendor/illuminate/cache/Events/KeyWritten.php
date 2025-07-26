<?php

namespace Illuminate\Cache\Events;

class KeyWritten extends CacheEvent
{





public $value;






public $seconds;










public function __construct($storeName, $key, $value, $seconds = null, $tags = [])
{
parent::__construct($storeName, $key, $tags);

$this->value = $value;
$this->seconds = $seconds;
}
}
