<?php









namespace Mockery\Matcher;

use ArrayAccess;

use function array_key_exists;
use function is_array;
use function sprintf;

class HasKey extends MatcherAbstract
{





public function __toString()
{
return sprintf('<HasKey[%s]>', $this->_expected);
}

/**
@template






*/
public function match(&$actual)
{
if (! is_array($actual) && ! $actual instanceof ArrayAccess) {
return false;
}

return array_key_exists($this->_expected, (array) $actual);
}
}
