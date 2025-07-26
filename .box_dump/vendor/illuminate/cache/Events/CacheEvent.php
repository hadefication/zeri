<?php

namespace Illuminate\Cache\Events;

abstract class CacheEvent
{





public $storeName;






public $key;






public $tags;








public function __construct($storeName, $key, array $tags = [])
{
$this->storeName = $storeName;
$this->key = $key;
$this->tags = $tags;
}







public function setTags($tags)
{
$this->tags = $tags;

return $this;
}
}
