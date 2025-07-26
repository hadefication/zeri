<?php

namespace Illuminate\Process;

use ArrayAccess;
use Illuminate\Support\Collection;

class ProcessPoolResults implements ArrayAccess
{





protected $results = [];






public function __construct(array $results)
{
$this->results = $results;
}






public function successful()
{
return $this->collect()->every(fn ($p) => $p->successful());
}






public function failed()
{
return ! $this->successful();
}






public function collect()
{
return new Collection($this->results);
}







public function offsetExists($offset): bool
{
return isset($this->results[$offset]);
}







public function offsetGet($offset): mixed
{
return $this->results[$offset];
}








public function offsetSet($offset, $value): void
{
$this->results[$offset] = $value;
}







public function offsetUnset($offset): void
{
unset($this->results[$offset]);
}
}
