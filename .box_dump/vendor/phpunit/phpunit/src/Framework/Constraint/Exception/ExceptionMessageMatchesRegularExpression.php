<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function preg_match;
use function sprintf;
use Exception;
use PHPUnit\Util\Exporter;

/**
@no-named-arguments


*/
final class ExceptionMessageMatchesRegularExpression extends Constraint
{
private readonly string $regularExpression;

public function __construct(string $regularExpression)
{
$this->regularExpression = $regularExpression;
}

public function toString(): string
{
return 'exception message matches ' . Exporter::export($this->regularExpression);
}








protected function matches(mixed $other): bool
{
$match = @preg_match($this->regularExpression, (string) $other);

if ($match === false) {
throw new \PHPUnit\Framework\Exception(
sprintf(
'Invalid expected exception message regular expression given: %s',
$this->regularExpression,
),
);
}

return $match === 1;
}







protected function failureDescription(mixed $other): string
{
return sprintf(
"exception message '%s' matches '%s'",
$other,
$this->regularExpression,
);
}
}
