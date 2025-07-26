<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery;
use Mockery\Generator\MockConfiguration;
use function array_reduce;
use function interface_exists;
use function ltrim;
use function str_replace;

class InterfacePass implements Pass
{




public function apply($code, MockConfiguration $config)
{
foreach ($config->getTargetInterfaces() as $i) {
$name = ltrim($i->getName(), '\\');
if (! interface_exists($name)) {
Mockery::declareInterface($name);
}
}

$interfaces = array_reduce($config->getTargetInterfaces(), static function ($code, $i) {
return $code . ', \\' . ltrim($i->getName(), '\\');
}, '');

return str_replace('implements MockInterface', 'implements MockInterface' . $interfaces, $code);
}
}
