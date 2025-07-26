<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function preg_replace;




class RemoveDestructorPass implements Pass
{




public function apply($code, MockConfiguration $config)
{
$target = $config->getTargetClass();

if (! $target) {
return $code;
}

if (! $config->isMockOriginalDestructor()) {
return preg_replace('/public function __destruct\(\)\s+\{.*?\}/sm', '', $code);
}

return $code;
}
}
