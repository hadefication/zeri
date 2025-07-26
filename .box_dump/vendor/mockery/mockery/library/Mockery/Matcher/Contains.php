<?php









namespace Mockery\Matcher;

use function array_values;
use function implode;

class Contains extends MatcherAbstract
{





public function __toString()
{
$elements = [];
foreach ($this->_expected as $v) {
$elements[] = (string) $v;
}

return '<Contains[' . implode(', ', $elements) . ']>';
}

/**
@template






*/
public function match(&$actual)
{
$values = array_values($actual);
foreach ($this->_expected as $exp) {
$match = false;
foreach ($values as $val) {
if ($exp === $val || $exp == $val) {
$match = true;
break;
}
}

if ($match === false) {
return false;
}
}

return true;
}
}
