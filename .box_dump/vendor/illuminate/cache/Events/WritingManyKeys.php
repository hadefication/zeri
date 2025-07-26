<?php

namespace Illuminate\Cache\Events;

class WritingManyKeys extends CacheEvent
{





public $keys;






public $values;






public $seconds;










public function __construct($storeName, $keys, $values, $seconds = null, $tags = [])
{
parent::__construct($storeName, $keys[0], $tags);

$this->keys = $keys;
$this->values = $values;
$this->seconds = $seconds;
}
}
