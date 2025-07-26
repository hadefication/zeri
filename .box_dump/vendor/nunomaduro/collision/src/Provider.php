<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Whoops\Run;
use Whoops\RunInterface;






final class Provider
{



private RunInterface $run;




private Handler $handler;




public function __construct(?RunInterface $run = null, ?Handler $handler = null)
{
$this->run = $run ?: new Run;
$this->handler = $handler ?: new Handler;
}




public function register(): self
{
$this->run->pushHandler($this->handler)
->register();

return $this;
}




public function getHandler(): Handler
{
return $this->handler;
}
}
