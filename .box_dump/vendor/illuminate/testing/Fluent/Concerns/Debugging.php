<?php

namespace Illuminate\Testing\Fluent\Concerns;

use Illuminate\Support\Traits\Dumpable;

trait Debugging
{
use Dumpable;







public function dump(?string $prop = null): static
{
dump($this->prop($prop));

return $this;
}







abstract protected function prop(?string $key = null);
}
