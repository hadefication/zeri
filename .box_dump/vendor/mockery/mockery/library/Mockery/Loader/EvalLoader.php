<?php









namespace Mockery\Loader;

use Mockery\Generator\MockDefinition;

use function class_exists;

class EvalLoader implements Loader
{





public function load(MockDefinition $definition)
{
if (class_exists($definition->getClassName(), false)) {
return;
}

eval('?>' . $definition->getCode());
}
}
