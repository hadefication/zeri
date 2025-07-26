<?php

namespace Illuminate\Contracts\Bus;

interface Dispatcher
{






public function dispatch($command);










public function dispatchSync($command, $handler = null);








public function dispatchNow($command, $handler = null);







public function hasCommandHandler($command);







public function getCommandHandler($command);







public function pipeThrough(array $pipes);







public function map(array $map);
}
