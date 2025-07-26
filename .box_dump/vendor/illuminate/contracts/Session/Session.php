<?php

namespace Illuminate\Contracts\Session;

interface Session
{





public function getName();







public function setName($name);






public function getId();







public function setId($id);






public function start();






public function save();






public function all();







public function exists($key);







public function has($key);








public function get($key, $default = null);








public function pull($key, $default = null);








public function put($key, $value = null);








public function flash(string $key, $value = true);






public function token();






public function regenerateToken();







public function remove($key);







public function forget($keys);






public function flush();






public function invalidate();







public function regenerate($destroy = false);







public function migrate($destroy = false);






public function isStarted();






public function previousUrl();







public function setPreviousUrl($url);






public function getHandler();






public function handlerNeedsRequest();







public function setRequestOnHandler($request);
}
