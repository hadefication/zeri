<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use Mockery\Generator\TargetClassInterface;
use function preg_replace;







class RemoveBuiltinMethodsThatAreFinalPass implements Pass
{
protected $methods = [
'__wakeup' => '/public function __wakeup\(\)\s+\{.*?\}/sm',
'__toString' => '/public function __toString\(\)\s+(:\s+string)?\s*\{.*?\}/sm',
];





public function apply($code, MockConfiguration $config)
{
$target = $config->getTargetClass();

if (! $target instanceof TargetClassInterface) {
return $code;
}

foreach ($target->getMethods() as $method) {
if (! $method->isFinal()) {
continue;
}

if (! isset($this->methods[$method->getName()])) {
continue;
}

$code = preg_replace($this->methods[$method->getName()], '', $code);
}

return $code;
}
}
