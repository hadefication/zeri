<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use function array_map;
use function count;
use function preg_match;
use function preg_quote;
use function preg_replace;
use PHPUnit\Framework\ExpectationFailedException;

/**
@no-named-arguments
*/
final class LogicalNot extends UnaryOperator
{
public static function negate(string $string): string
{
$positives = [
'contains ',
'exists',
'has ',
'is ',
'are ',
'matches ',
'starts with ',
'ends with ',
'reference ',
'not not ',
];

$negatives = [
'does not contain ',
'does not exist',
'does not have ',
'is not ',
'are not ',
'does not match ',
'starts not with ',
'ends not with ',
'don\'t reference ',
'not ',
];

preg_match('/(\'[\w\W]*\')([\w\W]*)("[\w\W]*")/i', $string, $matches);

if (count($matches) === 0) {
preg_match('/(\'[\w\W]*\')([\w\W]*)(\'[\w\W]*\')/i', $string, $matches);
}

$positives = array_map(
static fn (string $s) => '/\\b' . preg_quote($s, '/') . '/',
$positives,
);

if (count($matches) > 0) {
$nonInput = $matches[2];

$negatedString = preg_replace(
'/' . preg_quote($nonInput, '/') . '/',
preg_replace(
$positives,
$negatives,
$nonInput,
),
$string,
);
} else {
$negatedString = preg_replace(
$positives,
$negatives,
$string,
);
}

return $negatedString;
}




public function operator(): string
{
return 'not';
}






public function precedence(): int
{
return 5;
}







protected function matches(mixed $other): bool
{
return !$this->constraint()->evaluate($other, '', true);
}





protected function transformString(string $string): string
{
return self::negate($string);
}








protected function reduce(): Constraint
{
$constraint = $this->constraint();

if ($constraint instanceof self) {
return $constraint->constraint()->reduce();
}

return parent::reduce();
}
}
