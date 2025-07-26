<?php

namespace Illuminate\Cache\Events;

class RetrievingManyKeys extends CacheEvent
{





public $keys;








public function __construct($storeName, $keys, array $tags = [])
{
parent::__construct($storeName, $keys[0] ?? '', $tags);

$this->keys = $keys;
}
}
