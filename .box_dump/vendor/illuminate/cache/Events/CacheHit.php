<?php

namespace Illuminate\Cache\Events;

class CacheHit extends CacheEvent
{





public $value;









public function __construct($storeName, $key, $value, array $tags = [])
{
parent::__construct($storeName, $key, $tags);

$this->value = $value;
}
}
