<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;

/**
@no-named-arguments
*/
final class TraversableContainsOnly extends Constraint
{
private readonly Constraint $constraint;
private readonly string $type;






public function __construct(string $type, bool $isNativeType = true)
{
if ($isNativeType) {
$this->constraint = new IsType($type);
} else {
$this->constraint = new IsInstanceOf($type);
}

$this->type = $type;
}













public function evaluate(mixed $other, string $description = '', bool $returnResult = false): bool
{
$success = true;

foreach ($other as $item) {
if (!$this->constraint->evaluate($item, '', true)) {
$success = false;

break;
}
}

if (!$success && !$returnResult) {
$this->fail($other, $description);
}

return $success;
}




public function toString(): string
{
return 'contains only values of type "' . $this->type . '"';
}
}
