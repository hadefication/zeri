<?php









namespace Mockery;

use Closure;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use UnexpectedValueException;

use function class_exists;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;
use function strlen;
use function unserialize;




final class Instantiator
{
/**
@template







*/
public function instantiate($className): object
{
return $this->buildFactory($className)();
}




private function attemptInstantiationViaUnSerialization(
ReflectionClass $reflectionClass,
string $serializedString
): void {
set_error_handler(static function ($code, $message, $file, $line) use ($reflectionClass, &$error): void {
$msg = sprintf(
'Could not produce an instance of "%s" via un-serialization, since an error was triggered in file "%s" at line "%d"',
$reflectionClass->getName(),
$file,
$line
);

$error = new UnexpectedValueException($msg, 0, new Exception($message, $code));
});

try {
unserialize($serializedString);
} catch (Exception $exception) {
restore_error_handler();

throw new UnexpectedValueException(
sprintf(
'An exception was raised while trying to instantiate an instance of "%s" via un-serialization',
$reflectionClass->getName()
),
0,
$exception
);
}

restore_error_handler();

if ($error instanceof UnexpectedValueException) {
throw $error;
}
}




private function buildFactory(string $className): Closure
{
$reflectionClass = $this->getReflectionClass($className);

if ($this->isInstantiableViaReflection($reflectionClass)) {
return static function () use ($reflectionClass) {
return $reflectionClass->newInstanceWithoutConstructor();
};
}

$serializedString = sprintf('O:%d:"%s":0:{}', strlen($className), $className);

$this->attemptInstantiationViaUnSerialization($reflectionClass, $serializedString);

return static function () use ($serializedString) {
return unserialize($serializedString);
};
}




private function getReflectionClass(string $className): ReflectionClass
{
if (! class_exists($className)) {
throw new InvalidArgumentException(sprintf('Class:%s does not exist', $className));
}

$reflection = new ReflectionClass($className);

if ($reflection->isAbstract()) {
throw new InvalidArgumentException(sprintf('Class:%s is an abstract class', $className));
}

return $reflection;
}




private function hasInternalAncestors(ReflectionClass $reflectionClass): bool
{
do {
if ($reflectionClass->isInternal()) {
return true;
}
} while ($reflectionClass = $reflectionClass->getParentClass());

return false;
}




private function isInstantiableViaReflection(ReflectionClass $reflectionClass): bool
{
return ! ($reflectionClass->isInternal() && $reflectionClass->isFinal());
}
}
