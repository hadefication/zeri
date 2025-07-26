<?php declare(strict_types=1);








namespace PHPUnit\Framework\Constraint;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;
use function explode;
use function implode;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function strtr;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

/**
@no-named-arguments
*/
final class StringMatchesFormatDescription extends Constraint
{
private readonly string $formatDescription;

public function __construct(string $formatDescription)
{
$this->formatDescription = $formatDescription;
}

public function toString(): string
{
return 'matches format description:' . PHP_EOL . $this->formatDescription;
}





protected function matches(mixed $other): bool
{
$other = $this->convertNewlines($other);

$matches = preg_match(
$this->regularExpressionForFormatDescription(
$this->convertNewlines($this->formatDescription),
),
$other,
);

return $matches > 0;
}

protected function failureDescription(mixed $other): string
{
return 'string matches format description';
}

protected function additionalFailureDescription(mixed $other): string
{
$from = explode("\n", $this->formatDescription);
$to = explode("\n", $this->convertNewlines($other));

foreach ($from as $index => $line) {
if (isset($to[$index]) && $line !== $to[$index]) {
$line = $this->regularExpressionForFormatDescription($line);

if (preg_match($line, $to[$index]) > 0) {
$from[$index] = $to[$index];
}
}
}

$from = implode("\n", $from);
$to = implode("\n", $to);

return $this->differ()->diff($from, $to);
}

private function regularExpressionForFormatDescription(string $string): string
{
$string = strtr(
preg_quote($string, '/'),
[
'%%' => '%',
'%e' => preg_quote(DIRECTORY_SEPARATOR, '/'),
'%s' => '[^\r\n]+',
'%S' => '[^\r\n]*',
'%a' => '.+?',
'%A' => '.*?',
'%w' => '\s*',
'%i' => '[+-]?\d+',
'%d' => '\d+',
'%x' => '[0-9a-fA-F]+',
'%f' => '[+-]?(?:\d+|(?=\.\d))(?:\.\d+)?(?:[Ee][+-]?\d+)?',
'%c' => '.',
'%0' => '\x00',
],
);

return '/^' . $string . '$/s';
}

private function convertNewlines(string $text): string
{
return preg_replace('/\r\n/', "\n", $text);
}

private function differ(): Differ
{
return new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n"));
}
}
