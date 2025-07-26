<?php









namespace Mockery\Matcher;

use ArrayAccess;

use function in_array;
use function is_array;

class HasValue extends MatcherAbstract
{





public function __toString()
{
return '<HasValue[' . (string) $this->_expected . ']>';
}

/**
@template






*/
public function match(&$actual)
{
if (! is_array($actual) && ! $actual instanceof ArrayAccess) {
return false;
}

return in_array($this->_expected, (array) $actual, true);
}
}
