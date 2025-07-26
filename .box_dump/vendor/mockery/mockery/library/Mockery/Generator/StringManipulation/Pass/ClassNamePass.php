<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function ltrim;
use function str_replace;

class ClassNamePass implements Pass
{




public function apply($code, MockConfiguration $config)
{
$namespace = $config->getNamespaceName();

$namespace = ltrim($namespace, '\\');

$className = $config->getShortName();

$code = str_replace('namespace Mockery;', $namespace !== '' ? 'namespace ' . $namespace . ';' : '', $code);

return str_replace('class Mock', 'class ' . $className, $code);
}
}
