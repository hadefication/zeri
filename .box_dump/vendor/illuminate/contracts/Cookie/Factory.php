<?php

namespace Illuminate\Contracts\Cookie;

interface Factory
{














public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);














public function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);









public function forget($name, $path = null, $domain = null);
}
