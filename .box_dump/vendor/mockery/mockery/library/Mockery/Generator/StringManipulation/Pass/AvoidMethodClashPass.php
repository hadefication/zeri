<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function array_map;
use function in_array;
use function preg_replace;
use function sprintf;
use function str_replace;

class AvoidMethodClashPass implements Pass
{




public function apply($code, MockConfiguration $config)
{
$names = array_map(static function ($method) {
return $method->getName();
}, $config->getMethodsToMock());

foreach (['allows', 'expects'] as $method) {
if (in_array($method, $names, true)) {
$code = preg_replace(sprintf('#// start method %s.*// end method %s#ms', $method, $method), '', $code);

$code = str_replace(' implements MockInterface', ' implements LegacyMockInterface', $code);
}
}

return $code;
}
}
