<?php

namespace Illuminate\Console\Concerns;

use Illuminate\Console\Signals;
use Illuminate\Support\Collection;

trait InteractsWithSignals
{





protected $signals;

/**
@template






*/
public function trap($signals, $callback)
{
Signals::whenAvailable(function () use ($signals, $callback) {
$this->signals ??= new Signals(
$this->getApplication()->getSignalRegistry(),
);

Collection::wrap(value($signals))
->each(fn ($signal) => $this->signals->register($signal, $callback));
});
}








public function untrap()
{
if (! is_null($this->signals)) {
$this->signals->unregister();

$this->signals = null;
}
}
}
