<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function implode;
use function str_replace;

class ClassAttributesPass implements Pass
{




public function apply($code, MockConfiguration $config)
{
$class = $config->getTargetClass();

if (! $class) {
return $code;
}


$attributes = $class->getAttributes();

if ($attributes !== []) {
return str_replace('#[\AllowDynamicProperties]', '#[' . implode(',', $attributes) . ']', $code);
}

return $code;
}
}
