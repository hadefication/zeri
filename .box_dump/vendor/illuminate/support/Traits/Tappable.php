<?php

namespace Illuminate\Support\Traits;

trait Tappable
{






public function tap($callback = null)
{
return tap($this, $callback);
}
}
