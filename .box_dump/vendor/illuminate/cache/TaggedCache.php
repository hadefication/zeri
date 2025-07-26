<?php

namespace Illuminate\Cache;

use Illuminate\Cache\Events\CacheFlushed;
use Illuminate\Cache\Events\CacheFlushing;
use Illuminate\Contracts\Cache\Store;

class TaggedCache extends Repository
{
use RetrievesMultipleKeys {
putMany as putManyAlias;
}






protected $tags;







public function __construct(Store $store, TagSet $tags)
{
parent::__construct($store);

$this->tags = $tags;
}








public function putMany(array $values, $ttl = null)
{
if ($ttl === null) {
return $this->putManyForever($values);
}

return $this->putManyAlias($values, $ttl);
}








public function increment($key, $value = 1)
{
return $this->store->increment($this->itemKey($key), $value);
}








public function decrement($key, $value = 1)
{
return $this->store->decrement($this->itemKey($key), $value);
}






public function flush()
{
$this->event(new CacheFlushing($this->getName()));

$this->tags->reset();

$this->event(new CacheFlushed($this->getName()));

return true;
}




protected function itemKey($key)
{
return $this->taggedItemKey($key);
}







public function taggedItemKey($key)
{
return sha1($this->tags->getNamespace()).':'.$key;
}







protected function event($event)
{
if (method_exists($event, 'setTags')) {
$event->setTags($this->tags->getNames());
}

parent::event($event);
}






public function getTags()
{
return $this->tags;
}
}
