<?php









namespace Mockery\Matcher;

class IsEqual extends MatcherAbstract
{





public function __toString()
{
return '<IsEqual>';
}

/**
@template






*/
public function match(&$actual)
{
return $this->_expected == $actual;
}
}
