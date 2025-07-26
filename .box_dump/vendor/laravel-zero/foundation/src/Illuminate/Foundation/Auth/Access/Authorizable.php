<?php

namespace Illuminate\Foundation\Auth\Access;

use Illuminate\Contracts\Auth\Access\Gate;

trait Authorizable
{







public function can($abilities, $arguments = [])
{
return app(Gate::class)->forUser($this)->check($abilities, $arguments);
}








public function canAny($abilities, $arguments = [])
{
return app(Gate::class)->forUser($this)->any($abilities, $arguments);
}








public function cant($abilities, $arguments = [])
{
return ! $this->can($abilities, $arguments);
}








public function cannot($abilities, $arguments = [])
{
return $this->cant($abilities, $arguments);
}
}
