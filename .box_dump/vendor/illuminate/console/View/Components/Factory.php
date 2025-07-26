<?php

namespace Illuminate\Console\View\Components;

use InvalidArgumentException;

















class Factory
{





protected $output;






public function __construct($output)
{
$this->output = $output;
}










public function __call($method, $parameters)
{
$component = '\Illuminate\Console\View\Components\\'.ucfirst($method);

throw_unless(class_exists($component), new InvalidArgumentException(sprintf(
'Console component [%s] not found.', $method
)));

return with(new $component($this->output))->render(...$parameters);
}
}
