<?php









namespace Mockery\Generator\StringManipulation\Pass;

use Mockery\Generator\MockConfiguration;
use function strrpos;
use function substr;
use const PHP_VERSION_ID;







class RemoveUnserializeForInternalSerializableClassesPass implements Pass
{
public const DUMMY_METHOD_DEFINITION = 'public function unserialize(string $data): void {} ';

public const DUMMY_METHOD_DEFINITION_LEGACY = 'public function unserialize($string) {} ';





public function apply($code, MockConfiguration $config)
{
$target = $config->getTargetClass();

if (! $target) {
return $code;
}

if (! $target->hasInternalAncestor() || ! $target->implementsInterface('Serializable')) {
return $code;
}

return $this->appendToClass(
$code,
PHP_VERSION_ID < 80100 ? self::DUMMY_METHOD_DEFINITION_LEGACY : self::DUMMY_METHOD_DEFINITION
);
}

protected function appendToClass($class, $code)
{
$lastBrace = strrpos($class, '}');
return substr($class, 0, $lastBrace) . $code . "\n    }\n";
}
}
