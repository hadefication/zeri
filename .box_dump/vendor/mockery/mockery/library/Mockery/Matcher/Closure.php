<?php









namespace Mockery\Matcher;

class Closure extends MatcherAbstract
{





public function __toString()
{
return '<Closure===true>';
}

/**
@template






*/
public function match(&$actual)
{
return ($this->_expected)($actual) === true;
}
}
