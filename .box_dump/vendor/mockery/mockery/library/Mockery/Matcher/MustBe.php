<?php









namespace Mockery\Matcher;

use function is_object;




class MustBe extends MatcherAbstract
{





public function __toString()
{
return '<MustBe>';
}

/**
@template






*/
public function match(&$actual)
{
if (! is_object($actual)) {
return $this->_expected === $actual;
}

return $this->_expected == $actual;
}
}
