<?php

namespace Illuminate\Contracts\Pipeline;

use Closure;

interface Pipeline
{






public function send($passable);







public function through($pipes);







public function via($method);







public function then(Closure $destination);
}
