<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function sprintf;
use function str_contains;
use PHPUnit\Util\Exporter;

/**
@no-named-arguments


*/
final class ExceptionMessageIsOrContains extends Constraint
{
private readonly string $expectedMessage;

public function __construct(string $expectedMessage)
{
$this->expectedMessage = $expectedMessage;
}

public function toString(): string
{
if ($this->expectedMessage === '') {
return 'exception message is empty';
}

return 'exception message contains ' . Exporter::export($this->expectedMessage);
}

protected function matches(mixed $other): bool
{
if ($this->expectedMessage === '') {
return $other === '';
}

return str_contains((string) $other, $this->expectedMessage);
}







protected function failureDescription(mixed $other): string
{
if ($this->expectedMessage === '') {
return sprintf(
"exception message is empty but is '%s'",
$other,
);
}

return sprintf(
"exception message '%s' contains '%s'",
$other,
$this->expectedMessage,
);
}
}
