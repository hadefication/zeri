<?php

namespace Illuminate\Contracts\Support;

use Countable;

interface MessageBag extends Arrayable, Countable
{





public function keys();








public function add($key, $message);







public function merge($messages);







public function has($key);








public function first($key = null, $format = null);








public function get($key, $format = null);







public function all($format = null);







public function forget($key);






public function getMessages();






public function getFormat();







public function setFormat($format = ':message');






public function isEmpty();






public function isNotEmpty();
}
