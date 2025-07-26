<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function sprintf;
use PHPUnit\Util\Filter;
use Throwable;

/**
@no-named-arguments


*/
final class Exception extends Constraint
{
private readonly string $className;

public function __construct(string $className)
{
$this->className = $className;
}




public function toString(): string
{
return sprintf(
'exception of type "%s"',
$this->className,
);
}





protected function matches(mixed $other): bool
{
return $other instanceof $this->className;
}









protected function failureDescription(mixed $other): string
{
if ($other === null) {
return sprintf(
'exception of type "%s" is thrown',
$this->className,
);
}

$message = '';

if ($other instanceof Throwable) {
$message = '. Message was: "' . $other->getMessage() . '" at'
. "\n" . Filter::stackTraceFromThrowableAsString($other);
}

return sprintf(
'exception of type "%s" matches expected exception "%s"%s',
$other::class,
$this->className,
$message,
);
}
}
