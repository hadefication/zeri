<?php









namespace Mockery\Matcher;

use function preg_match;

class Pattern extends MatcherAbstract
{





public function __toString()
{
return '<Pattern>';
}

/**
@template






*/
public function match(&$actual)
{
return preg_match($this->_expected, (string) $actual) >= 1;
}
}
