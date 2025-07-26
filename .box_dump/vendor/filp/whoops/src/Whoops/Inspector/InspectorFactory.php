<?php





namespace Whoops\Inspector;

use Whoops\Exception\Inspector;

class InspectorFactory implements InspectorFactoryInterface
{




public function create($exception)
{
return new Inspector($exception, $this);
}
}
