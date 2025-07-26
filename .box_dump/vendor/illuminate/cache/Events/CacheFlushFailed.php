<?php

namespace Illuminate\Cache\Events;

class CacheFlushFailed
{





public $storeName;






public $tags;







public function __construct($storeName, array $tags = [])
{
$this->storeName = $storeName;
$this->tags = $tags;
}







public function setTags($tags)
{
$this->tags = $tags;

return $this;
}
}
