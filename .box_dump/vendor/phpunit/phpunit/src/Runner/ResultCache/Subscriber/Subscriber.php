<?php declare(strict_types=1);








namespace PHPUnit\Runner\ResultCache;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private ResultCacheHandler $handler;

public function __construct(ResultCacheHandler $handler)
{
$this->handler = $handler;
}

protected function handler(): ResultCacheHandler
{
return $this->handler;
}
}
