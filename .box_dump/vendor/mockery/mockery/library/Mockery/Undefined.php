<?php









namespace Mockery;

use function spl_object_hash;

class Undefined
{








public function __call($method, array $args)
{
return $this;
}






public function __toString()
{
return self::class . ':' . spl_object_hash($this);
}
}
