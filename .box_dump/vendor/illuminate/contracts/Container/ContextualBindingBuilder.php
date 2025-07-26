<?php

namespace Illuminate\Contracts\Container;

interface ContextualBindingBuilder
{






public function needs($abstract);







public function give($implementation);







public function giveTagged($tag);








public function giveConfig($key, $default = null);
}
