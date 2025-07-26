<?php

namespace Illuminate\View;

use ErrorException;
use Illuminate\Container\Container;
use Illuminate\Support\Reflector;

class ViewException extends ErrorException
{





public function report()
{
$exception = $this->getPrevious();

if (Reflector::isCallable($reportCallable = [$exception, 'report'])) {
return Container::getInstance()->call($reportCallable);
}

return false;
}







public function render($request)
{
$exception = $this->getPrevious();

if ($exception && method_exists($exception, 'render')) {
return $exception->render($request);
}
}
}
