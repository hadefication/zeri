<?php

namespace Illuminate\Support\Traits;

use BadMethodCallException;
use Error;

trait ForwardsCalls
{










protected function forwardCallTo($object, $method, $parameters)
{
try {
return $object->{$method}(...$parameters);
} catch (Error|BadMethodCallException $e) {
$pattern = '~^Call to undefined method (?P<class>[^:]+)::(?P<method>[^\(]+)\(\)$~';

if (! preg_match($pattern, $e->getMessage(), $matches)) {
throw $e;
}

if ($matches['class'] != get_class($object) ||
$matches['method'] != $method) {
throw $e;
}

static::throwBadMethodCallException($method);
}
}











protected function forwardDecoratedCallTo($object, $method, $parameters)
{
$result = $this->forwardCallTo($object, $method, $parameters);

return $result === $object ? $this : $result;
}









protected static function throwBadMethodCallException($method)
{
throw new BadMethodCallException(sprintf(
'Call to undefined method %s::%s()', static::class, $method
));
}
}
