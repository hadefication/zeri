<?php









namespace Mockery\Matcher;

use function implode;
use function is_object;
use function method_exists;

class Ducktype extends MatcherAbstract
{





public function __toString()
{
return '<Ducktype[' . implode(', ', $this->_expected) . ']>';
}

/**
@template






*/
public function match(&$actual)
{
if (! is_object($actual)) {
return false;
}

foreach ($this->_expected as $method) {
if (! method_exists($actual, $method)) {
return false;
}
}

return true;
}
}
