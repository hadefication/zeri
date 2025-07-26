<?php

namespace Illuminate\Foundation\Bus;

use Closure;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Fluent;

trait Dispatchable
{






public static function dispatch(...$arguments)
{
return static::newPendingDispatch(new static(...$arguments));
}








public static function dispatchIf($boolean, ...$arguments)
{
if ($boolean instanceof Closure) {
$dispatchable = new static(...$arguments);

return value($boolean, $dispatchable)
? static::newPendingDispatch($dispatchable)
: new Fluent;
}

return value($boolean)
? static::newPendingDispatch(new static(...$arguments))
: new Fluent;
}








public static function dispatchUnless($boolean, ...$arguments)
{
if ($boolean instanceof Closure) {
$dispatchable = new static(...$arguments);

return ! value($boolean, $dispatchable)
? static::newPendingDispatch($dispatchable)
: new Fluent;
}

return ! value($boolean)
? static::newPendingDispatch(new static(...$arguments))
: new Fluent;
}









public static function dispatchSync(...$arguments)
{
return app(Dispatcher::class)->dispatchSync(new static(...$arguments));
}







public static function dispatchAfterResponse(...$arguments)
{
return self::dispatch(...$arguments)->afterResponse();
}







public static function withChain($chain)
{
return new PendingChain(static::class, $chain);
}







protected static function newPendingDispatch($job)
{
return new PendingDispatch($job);
}
}
