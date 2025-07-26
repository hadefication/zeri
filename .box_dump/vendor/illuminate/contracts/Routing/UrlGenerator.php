<?php

namespace Illuminate\Contracts\Routing;

interface UrlGenerator
{





public function current();







public function previous($fallback = false);









public function to($path, $extra = [], $secure = null);








public function secure($path, $parameters = []);








public function asset($path, $secure = null);











public function route($name, $parameters = [], $absolute = true);












public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true);










public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true);










public function query($path, $query = [], $extra = [], $secure = null);









public function action($action, $parameters = [], $absolute = true);






public function getRootControllerNamespace();







public function setRootControllerNamespace($rootNamespace);
}
