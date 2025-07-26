<?php









namespace Mockery\Matcher;

use function in_array;

class AnyOf extends MatcherAbstract
{





public function __toString()
{
return '<AnyOf>';
}

/**
@template







*/
public function match(&$actual)
{
return in_array($actual, $this->_expected, true);
}
}
