<?php

namespace Illuminate\Support\Traits;

trait Dumpable
{






public function dd(...$args)
{
dd($this, ...$args);
}







public function dump(...$args)
{
dump($this, ...$args);

return $this;
}
}
