<?php









namespace Mockery\Matcher;

class IsSame extends MatcherAbstract
{





public function __toString()
{
return '<IsSame>';
}

/**
@template






*/
public function match(&$actual)
{
return $this->_expected === $actual;
}
}
