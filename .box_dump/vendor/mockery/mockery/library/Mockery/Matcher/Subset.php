<?php









namespace Mockery\Matcher;

use function array_replace_recursive;
use function implode;
use function is_array;

class Subset extends MatcherAbstract
{
private $expected;

private $strict = true;





public function __construct(array $expected, $strict = true)
{
$this->expected = $expected;
$this->strict = $strict;
}






public function __toString()
{
return '<Subset' . $this->formatArray($this->expected) . '>';
}






public static function loose(array $expected)
{
return new static($expected, false);
}

/**
@template






*/
public function match(&$actual)
{
if (! is_array($actual)) {
return false;
}

if ($this->strict) {
return $actual === array_replace_recursive($actual, $this->expected);
}

return $actual == array_replace_recursive($actual, $this->expected);
}






public static function strict(array $expected)
{
return new static($expected, true);
}






protected function formatArray(array $array)
{
$elements = [];
foreach ($array as $k => $v) {
$elements[] = $k . '=' . (is_array($v) ? $this->formatArray($v) : (string) $v);
}

return '[' . implode(', ', $elements) . ']';
}
}
