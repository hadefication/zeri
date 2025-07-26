<?php









namespace Mockery\Matcher;

class MultiArgumentClosure extends MatcherAbstract implements ArgumentListMatcher
{





public function __toString()
{
return '<MultiArgumentClosure===true>';
}

/**
@template








*/
public function match(&$actual)
{
return ($this->_expected)(...$actual) === true;
}
}
